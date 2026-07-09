<?php

namespace App\Services;

use App\Models\ATMCard;
use App\Models\ATMCardRequest;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ATMCardRequestService
{
    /**
     * @return array{card: ATMCard, pin: string}
     */
    public function approve(ATMCardRequest $cardRequest, int $employeeId): array
    {
        return DB::transaction(function () use ($cardRequest, $employeeId): array {
            $lockedRequest = ATMCardRequest::query()
                ->whereKey($cardRequest->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedRequest->status !== ATMCardRequest::STATUS_PENDING) {
                throw ValidationException::withMessages([
                    'card' => 'Only pending ATM-card requests can be approved.',
                ]);
            }

            $account = Account::query()
                ->whereKey($lockedRequest->account_id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $account->isActive()) {
                throw ValidationException::withMessages([
                    'card' => 'Only active accounts can receive ATM cards.',
                ]);
            }

            if ($this->hasActiveCard($account)) {
                throw ValidationException::withMessages([
                    'card' => 'This account already has an active ATM card.',
                ]);
            }

            $pin = (string) random_int(1000, 9999);

            $card = ATMCard::create([
                'account_id' => $account->id,
                'atm_card_request_id' => $lockedRequest->id,
                'card_number' => $this->generateCardNumber(),
                'pin_hash' => Hash::make($pin),
                'status' => ATMCard::STATUS_ACTIVE,
                'failed_attempts' => 0,
                'issued_by' => $employeeId,
                'issued_at' => now(),
                'expires_at' => now()->addYears(5),
            ]);

            $lockedRequest->update([
                'status' => ATMCardRequest::STATUS_APPROVED,
                'handled_by' => $employeeId,
                'processed_at' => now(),
                'rejection_reason' => null,
            ]);

            return [
                'card' => $card,
                'pin' => $pin,
            ];
        });
    }

    public function reject(ATMCardRequest $cardRequest, int $employeeId, string $reason): void
    {
        DB::transaction(function () use ($cardRequest, $employeeId, $reason): void {
            $lockedRequest = ATMCardRequest::query()
                ->whereKey($cardRequest->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedRequest->status !== ATMCardRequest::STATUS_PENDING) {
                throw ValidationException::withMessages([
                    'card' => 'Only pending ATM-card requests can be rejected.',
                ]);
            }

            $lockedRequest->update([
                'status' => ATMCardRequest::STATUS_REJECTED,
                'handled_by' => $employeeId,
                'processed_at' => now(),
                'rejection_reason' => $reason,
            ]);
        });
    }

    public function block(ATMCard $card): void
    {
        if ($card->status !== ATMCard::STATUS_ACTIVE) {
            throw ValidationException::withMessages([
                'card' => 'Only active ATM cards can be blocked.',
            ]);
        }

        $card->update([
            'status' => ATMCard::STATUS_BLOCKED,
            'failed_attempts' => 0,
        ]);
    }

    public function unblock(ATMCard $card): void
    {
        if ($card->status !== ATMCard::STATUS_BLOCKED) {
            throw ValidationException::withMessages([
                'card' => 'Only blocked ATM cards can be unblocked.',
            ]);
        }

        $card->update([
            'status' => ATMCard::STATUS_ACTIVE,
            'failed_attempts' => 0,
        ]);
    }

    public function createPending(Account $account): ATMCardRequest
    {
        return DB::transaction(function () use ($account): ATMCardRequest {
            $lockedAccount = Account::query()
                ->whereKey($account->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedAccount->isActive()) {
                throw ValidationException::withMessages([
                    'account' => 'Frozen accounts cannot request ATM cards.',
                ]);
            }

            if ($this->hasPendingRequest($lockedAccount)) {
                throw ValidationException::withMessages([
                    'card' => 'You already have a pending ATM-card request.',
                ]);
            }

            if ($this->hasActiveCard($lockedAccount)) {
                throw ValidationException::withMessages([
                    'card' => 'You already have an active ATM card.',
                ]);
            }

            return ATMCardRequest::create([
                'account_id' => $lockedAccount->id,
                'status' => ATMCardRequest::STATUS_PENDING,
                'requested_at' => now(),
            ]);
        });
    }

    private function hasPendingRequest(Account $account): bool
    {
        return $account->atmCardRequests()
            ->where('status', ATMCardRequest::STATUS_PENDING)
            ->exists();
    }

    private function hasActiveCard(Account $account): bool
    {
        return $account->atmCards()
            ->where('status', ATMCard::STATUS_ACTIVE)
            ->exists();
    }

    private function generateCardNumber(): string
    {
        do {
            $cardNumber = '5060'.str_pad((string) random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);
        } while (ATMCard::query()->where('card_number', $cardNumber)->exists());

        return $cardNumber;
    }
}
