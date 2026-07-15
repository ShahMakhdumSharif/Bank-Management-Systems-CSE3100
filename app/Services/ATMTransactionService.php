<?php

namespace App\Services;

use App\Models\Account;
use App\Models\ATMCard;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ATMTransactionService
{
    public function deposit(ATMCard $card, string $amount): Transaction
    {
        return DB::transaction(function () use ($card, $amount): Transaction {
            $lockedCard = $this->activeLockedCard($card);
            $lockedAccount = $this->activeLockedAccount($lockedCard);
            $normalizedAmount = $this->normalizeAmount($amount);

            $this->ensureDepositAllowed($normalizedAmount);

            $balanceBefore = $this->normalizeAmount($lockedAccount->balance);
            $balanceAfter = $this->add($balanceBefore, $normalizedAmount);

            $lockedAccount->update([
                'balance' => $balanceAfter,
            ]);

            $lockedCard->update([
                'last_used_at' => now(),
            ]);

            return $this->recordTransaction(
                $lockedAccount,
                Transaction::TYPE_ATM_DEPOSIT,
                $normalizedAmount,
                $balanceBefore,
                $balanceAfter,
                'Virtual ATM cash deposit.',
            );
        });
    }

    public function withdraw(ATMCard $card, string $amount): Transaction
    {
        return DB::transaction(function () use ($card, $amount): Transaction {
            $lockedCard = $this->activeLockedCard($card);
            $lockedAccount = $this->activeLockedAccount($lockedCard);
            $normalizedAmount = $this->normalizeAmount($amount);

            $this->ensureWithdrawalAllowed($lockedAccount, $normalizedAmount);

            $balanceBefore = $this->normalizeAmount($lockedAccount->balance);
            $balanceAfter = $this->subtract($balanceBefore, $normalizedAmount);

            $lockedAccount->update([
                'balance' => $balanceAfter,
            ]);

            $lockedCard->update([
                'last_used_at' => now(),
            ]);

            return $this->recordTransaction(
                $lockedAccount,
                Transaction::TYPE_ATM_WITHDRAWAL,
                $normalizedAmount,
                $balanceBefore,
                $balanceAfter,
                'Virtual ATM cash withdrawal.',
            );
        });
    }

    private function activeLockedCard(ATMCard $card): ATMCard
    {
        $lockedCard = ATMCard::query()
            ->whereKey($card->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($lockedCard->status !== ATMCard::STATUS_ACTIVE) {
            throw ValidationException::withMessages([
                'card' => 'Only active ATM cards can perform ATM transactions.',
            ]);
        }

        if ($lockedCard->expires_at !== null && $lockedCard->expires_at->isPast()) {
            $lockedCard->update([
                'status' => ATMCard::STATUS_EXPIRED,
            ]);

            throw ValidationException::withMessages([
                'card' => 'This ATM card has expired.',
            ]);
        }

        return $lockedCard;
    }

    private function activeLockedAccount(ATMCard $card): Account
    {
        $lockedAccount = Account::query()
            ->whereKey($card->account_id)
            ->lockForUpdate()
            ->firstOrFail();

        if (! $lockedAccount->isActive()) {
            throw ValidationException::withMessages([
                'account' => 'Frozen accounts cannot use ATM transactions.',
            ]);
        }

        return $lockedAccount;
    }

    private function ensureDepositAllowed(string $amount): void
    {
        $maximumDeposit = $this->moneyConfig('atm.deposit_limit');

        if ($this->toCents($amount) > $this->toCents($maximumDeposit)) {
            throw ValidationException::withMessages([
                'amount' => 'ATM deposits cannot exceed BDT '.number_format((float) $maximumDeposit, 2).'.',
            ]);
        }
    }

    private function ensureWithdrawalAllowed(Account $account, string $amount): void
    {
        $maximumWithdrawal = $this->moneyConfig('atm.withdrawal_limit');
        $minimumBalance = $this->moneyConfig('atm.minimum_balance');
        $dailyWithdrawalLimit = $this->moneyConfig('atm.daily_withdrawal_limit');
        $balanceBefore = $this->normalizeAmount($account->balance);
        $balanceAfter = $this->subtract($balanceBefore, $amount);

        if ($this->toCents($amount) > $this->toCents($maximumWithdrawal)) {
            throw ValidationException::withMessages([
                'amount' => 'ATM withdrawals cannot exceed BDT '.number_format((float) $maximumWithdrawal, 2).' per transaction.',
            ]);
        }

        if ($this->toCents($balanceAfter) < $this->toCents($minimumBalance)) {
            throw ValidationException::withMessages([
                'amount' => 'This withdrawal would reduce the account below the minimum ATM balance of BDT '.number_format((float) $minimumBalance, 2).'.',
            ]);
        }

        $withdrawnToday = $this->atmWithdrawnToday($account);

        if ($this->toCents($this->add($withdrawnToday, $amount)) > $this->toCents($dailyWithdrawalLimit)) {
            throw ValidationException::withMessages([
                'amount' => 'This withdrawal would exceed the daily ATM withdrawal limit of BDT '.number_format((float) $dailyWithdrawalLimit, 2).'.',
            ]);
        }
    }

    private function atmWithdrawnToday(Account $account): string
    {
        $total = Transaction::query()
            ->where('account_id', $account->id)
            ->where('type', Transaction::TYPE_ATM_WITHDRAWAL)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->whereBetween('created_at', [
                Carbon::today()->startOfDay(),
                Carbon::today()->endOfDay(),
            ])
            ->sum('amount');

        return $this->normalizeAmount($total);
    }

    private function recordTransaction(
        Account $account,
        string $type,
        string $amount,
        string $balanceBefore,
        string $balanceAfter,
        string $description,
    ): Transaction {
        return Transaction::create([
            'account_id' => $account->id,
            'reference' => $this->generateReference(),
            'type' => $type,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'status' => Transaction::STATUS_COMPLETED,
            'source' => Transaction::SOURCE_ATM,
            'description' => $description,
        ]);
    }

    private function generateReference(): string
    {
        do {
            $reference = 'ATM'.now()->format('ymdHis').random_int(1000, 9999);
        } while (Transaction::query()->where('reference', $reference)->exists());

        return $reference;
    }

    private function moneyConfig(string $key): string
    {
        return $this->normalizeAmount(config('bank.'.$key));
    }

    private function add(string $left, string $right): string
    {
        return number_format(((float) $left) + ((float) $right), 2, '.', '');
    }

    private function subtract(string $left, string $right): string
    {
        return number_format(((float) $left) - ((float) $right), 2, '.', '');
    }

    private function normalizeAmount(string|float|int|null $amount): string
    {
        return number_format((float) $amount, 2, '.', '');
    }

    private function toCents(string|float|int $amount): int
    {
        return (int) str_replace('.', '', $this->normalizeAmount($amount));
    }
}
