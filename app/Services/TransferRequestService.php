<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransferRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransferRequestService
{
    /**
     * @return array{receiver: Account, amount: string}
     */
    public function validateRequest(Account $senderAccount, string $receiverAccountNumber, string $amount): array
    {
        $receiverAccount = Account::query()
            ->where('account_number', $receiverAccountNumber)
            ->first();

        if ($receiverAccount === null) {
            throw ValidationException::withMessages([
                'receiver_account_number' => 'The receiver account number is not valid.',
            ]);
        }

        if ($senderAccount->is($receiverAccount)) {
            throw ValidationException::withMessages([
                'receiver_account_number' => 'You cannot transfer money to your own account.',
            ]);
        }

        if (! $senderAccount->isActive()) {
            throw ValidationException::withMessages([
                'account' => 'Frozen accounts cannot create transfer requests.',
            ]);
        }

        if (! $receiverAccount->isActive()) {
            throw ValidationException::withMessages([
                'receiver_account_number' => 'The receiver account is not active.',
            ]);
        }

        $normalizedAmount = number_format((float) $amount, 2, '.', '');
        $this->ensureMinimumBalanceAfterPendingTransfers($senderAccount, $normalizedAmount);

        return [
            'receiver' => $receiverAccount,
            'amount' => $normalizedAmount,
        ];
    }

    public function ensureMinimumBalanceAfterPendingTransfers(Account $senderAccount, string|float|int $amount = 0): void
    {
        $availableCents = $this->availableBalanceCents($senderAccount);
        $requestedCents = $this->toCents($amount);

        if ($requestedCents > $availableCents) {
            throw ValidationException::withMessages([
                'amount' => 'This transfer would bring your account below the minimum required balance of '
                    .config('bank.currency').' '.number_format($this->minimumBalanceCents() / 100, 2)
                    .'.',
            ]);
        }
    }

    public function ensureCanCreateAnotherTransfer(Account $senderAccount): void
    {
        if ($this->availableBalanceCents($senderAccount) <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'Your account must keep a minimum balance of '
                    .config('bank.currency').' '.number_format($this->minimumBalanceCents() / 100, 2).'.',
            ]);
        }
    }

    public function transferableBalance(Account $senderAccount): string
    {
        return number_format($this->availableBalanceCents($senderAccount) / 100, 2, '.', '');
    }

    public function createPending(Account $senderAccount, string $receiverAccountNumber, string $amount): TransferRequest
    {
        return DB::transaction(function () use ($senderAccount, $receiverAccountNumber, $amount): TransferRequest {
            $lockedSenderAccount = Account::query()
                ->whereKey($senderAccount->id)
                ->lockForUpdate()
                ->firstOrFail();

            $validated = $this->validateRequest($lockedSenderAccount, $receiverAccountNumber, $amount);
            $balanceBefore = $this->normalizeAmount($lockedSenderAccount->balance);
            $balanceAfter = $this->subtract($balanceBefore, $validated['amount']);

            $lockedSenderAccount->update([
                'balance' => $balanceAfter,
            ]);

            $transferRequest = TransferRequest::create([
                'sender_account_id' => $lockedSenderAccount->id,
                'receiver_account_id' => $validated['receiver']->id,
                'amount' => $validated['amount'],
                'status' => TransferRequest::STATUS_PENDING,
                'requested_at' => now(),
            ]);

            $this->recordTransaction(
                $lockedSenderAccount,
                Transaction::TYPE_TRANSFER_DEBIT,
                $validated['amount'],
                $balanceBefore,
                $balanceAfter,
                $transferRequest,
                $validated['receiver'],
                'Transfer request reserved from sender balance.',
                Transaction::STATUS_PENDING,
            );

            return $transferRequest;
        });
    }

    public function cancel(Account $senderAccount, TransferRequest $transferRequest): void
    {
        DB::transaction(function () use ($senderAccount, $transferRequest): void {
            $lockedTransferRequest = TransferRequest::query()
                ->whereKey($transferRequest->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedSenderAccount = Account::query()
                ->whereKey($senderAccount->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $lockedTransferRequest->sender_account_id !== (int) $lockedSenderAccount->id) {
                abort(403);
            }

            $this->refundPendingTransfer(
                $lockedSenderAccount,
                $lockedTransferRequest,
                TransferRequest::STATUS_CANCELLED,
                'Transfer request cancelled and reserved amount returned.',
            );
        });
    }

    public function reject(TransferRequest $transferRequest, int $employeeId, string $reason): void
    {
        DB::transaction(function () use ($transferRequest, $employeeId, $reason): void {
            $lockedTransferRequest = TransferRequest::query()
                ->whereKey($transferRequest->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedSenderAccount = Account::query()
                ->whereKey($lockedTransferRequest->sender_account_id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->refundPendingTransfer(
                $lockedSenderAccount,
                $lockedTransferRequest,
                TransferRequest::STATUS_REJECTED,
                'Transfer request rejected and reserved amount returned.',
                [
                    'handled_by' => $employeeId,
                    'rejection_reason' => $reason,
                ],
            );
        });
    }

    private function refundPendingTransfer(
        Account $senderAccount,
        TransferRequest $transferRequest,
        string $newStatus,
        string $description,
        array $extraTransferUpdates = [],
    ): void {
        if ($transferRequest->status !== TransferRequest::STATUS_PENDING) {
            throw ValidationException::withMessages([
                'transfer' => 'Only pending transfer requests can be refunded.',
            ]);
        }

        $balanceBefore = $this->normalizeAmount($senderAccount->balance);
        $balanceAfter = $this->add($balanceBefore, $transferRequest->amount);

        $senderAccount->update([
            'balance' => $balanceAfter,
        ]);

        $transferRequest->update($extraTransferUpdates + [
            'status' => $newStatus,
            'processed_at' => now(),
        ]);

        Transaction::query()
            ->where('transfer_request_id', $transferRequest->id)
            ->where('account_id', $senderAccount->id)
            ->where('type', Transaction::TYPE_TRANSFER_DEBIT)
            ->where('status', Transaction::STATUS_PENDING)
            ->update(['status' => Transaction::STATUS_REVERSED]);

        $this->recordTransaction(
            $senderAccount,
            Transaction::TYPE_TRANSFER_CREDIT,
            $transferRequest->amount,
            $balanceBefore,
            $balanceAfter,
            $transferRequest,
            $transferRequest->receiverAccount,
            $description,
        );
    }

    private function toCents(string|float|int $amount): int
    {
        $normalized = $this->normalizeAmount($amount);

        return (int) str_replace('.', '', $normalized);
    }

    private function availableBalanceCents(Account $senderAccount): int
    {
        $transferableCents = $this->toCents($senderAccount->balance)
            - $this->minimumBalanceCents();

        return max(0, $transferableCents);
    }

    private function minimumBalanceCents(): int
    {
        return $this->toCents(config('bank.minimum_transfer_balance', 500));
    }

    private function recordTransaction(
        Account $account,
        string $type,
        string|float|int $amount,
        string|float|int $balanceBefore,
        string|float|int $balanceAfter,
        TransferRequest $transferRequest,
        Account $relatedAccount,
        string $description,
        string $status = Transaction::STATUS_COMPLETED,
    ): Transaction {
        return Transaction::create([
            'account_id' => $account->id,
            'related_account_id' => $relatedAccount->id,
            'transfer_request_id' => $transferRequest->id,
            'reference' => $this->generateReference(),
            'type' => $type,
            'amount' => $this->normalizeAmount($amount),
            'balance_before' => $this->normalizeAmount($balanceBefore),
            'balance_after' => $this->normalizeAmount($balanceAfter),
            'status' => $status,
            'source' => Transaction::SOURCE_TRANSFER,
            'description' => $description,
        ]);
    }

    private function generateReference(): string
    {
        do {
            $reference = 'TRF'.now()->format('ymdHis').random_int(1000, 9999);
        } while (Transaction::query()->where('reference', $reference)->exists());

        return $reference;
    }

    private function add(string|float|int $left, string|float|int $right): string
    {
        return number_format(((float) $left) + ((float) $right), 2, '.', '');
    }

    private function subtract(string|float|int $left, string|float|int $right): string
    {
        return number_format(((float) $left) - ((float) $right), 2, '.', '');
    }

    private function normalizeAmount(string|float|int $amount): string
    {
        return number_format((float) $amount, 2, '.', '');
    }
}
