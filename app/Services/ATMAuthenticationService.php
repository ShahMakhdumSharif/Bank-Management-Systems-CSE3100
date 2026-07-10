<?php

namespace App\Services;

use App\Models\ATMCard;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ATMAuthenticationService
{
    private const MAX_FAILED_ATTEMPTS = 3;

    public function attempt(string $cardNumber, string $pin): ATMCard
    {
        $card = ATMCard::query()
            ->with('account.customer')
            ->where('card_number', $cardNumber)
            ->first();

        if ($card === null) {
            throw ValidationException::withMessages([
                'card_number' => 'The card number or PIN is incorrect.',
            ]);
        }

        if ($card->status !== ATMCard::STATUS_ACTIVE) {
            throw ValidationException::withMessages([
                'card_number' => 'This ATM card is not active.',
            ]);
        }

        if ($card->expires_at !== null && $card->expires_at->isPast()) {
            $card->update(['status' => ATMCard::STATUS_EXPIRED]);

            throw ValidationException::withMessages([
                'card_number' => 'This ATM card has expired.',
            ]);
        }

        if (! $card->account?->isActive()) {
            throw ValidationException::withMessages([
                'card_number' => 'This account is not active for ATM access.',
            ]);
        }

        if (! Hash::check($pin, $card->pin_hash)) {
            $failedAttempts = $card->failed_attempts + 1;

            $card->update([
                'failed_attempts' => $failedAttempts,
                'status' => $failedAttempts >= self::MAX_FAILED_ATTEMPTS
                    ? ATMCard::STATUS_BLOCKED
                    : $card->status,
            ]);

            throw ValidationException::withMessages([
                'pin' => $failedAttempts >= self::MAX_FAILED_ATTEMPTS
                    ? 'Too many incorrect PIN attempts. This ATM card has been blocked.'
                    : 'The card number or PIN is incorrect.',
            ]);
        }

        $card->update([
            'failed_attempts' => 0,
            'last_used_at' => now(),
        ]);

        return $card;
    }
}
