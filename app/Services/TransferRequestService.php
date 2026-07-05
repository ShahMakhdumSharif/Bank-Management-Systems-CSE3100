<?php

namespace App\Services;

use App\Models\Account;
use App\Models\TransferRequest;
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

        if ($this->toCents($normalizedAmount) > $this->toCents($senderAccount->balance)) {
            throw ValidationException::withMessages([
                'amount' => 'The transfer amount cannot be greater than your available balance.',
            ]);
        }

        return [
            'receiver' => $receiverAccount,
            'amount' => $normalizedAmount,
        ];
    }

    public function createPending(Account $senderAccount, string $receiverAccountNumber, string $amount): TransferRequest
    {
        $validated = $this->validateRequest($senderAccount, $receiverAccountNumber, $amount);

        return TransferRequest::create([
            'sender_account_id' => $senderAccount->id,
            'receiver_account_id' => $validated['receiver']->id,
            'amount' => $validated['amount'],
            'status' => TransferRequest::STATUS_PENDING,
            'requested_at' => now(),
        ]);
    }

    public function cancel(Account $senderAccount, TransferRequest $transferRequest): void
    {
        if (! $senderAccount->is($transferRequest->senderAccount)) {
            abort(403);
        }

        if ($transferRequest->status !== TransferRequest::STATUS_PENDING) {
            throw ValidationException::withMessages([
                'transfer' => 'Only pending transfer requests can be cancelled.',
            ]);
        }

        $transferRequest->update([
            'status' => TransferRequest::STATUS_CANCELLED,
            'processed_at' => now(),
        ]);
    }

    private function toCents(string|float|int $amount): int
    {
        $normalized = number_format((float) $amount, 2, '.', '');

        return (int) str_replace('.', '', $normalized);
    }
}
