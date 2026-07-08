<?php

namespace App\Services;

use App\Models\ATMCard;
use App\Models\ATMCardRequest;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ATMCardRequestService
{
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
}
