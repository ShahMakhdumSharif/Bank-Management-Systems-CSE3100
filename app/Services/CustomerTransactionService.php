<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CustomerTransactionService
{
    public function deposit(Account $account, string $amount): Transaction
    {
        return DB::transaction(function () use ($account, $amount): Transaction {
            $lockedAccount = $this->activeLockedAccount($account);
            $normalizedAmount = $this->normalizeAmount($amount);
            $balanceBefore = $this->normalizeAmount($lockedAccount->balance);
            $balanceAfter = $this->add($balanceBefore, $normalizedAmount);

            $lockedAccount->update([
                'balance' => $balanceAfter,
            ]);

            return $this->recordTransaction(
                $lockedAccount,
                Transaction::TYPE_CUSTOMER_DEPOSIT,
                $normalizedAmount,
                $balanceBefore,
                $balanceAfter,
                'Customer dashboard deposit.',
            );
        });
    }

    public function withdraw(Account $account, string $amount): Transaction
    {
        return DB::transaction(function () use ($account, $amount): Transaction {
            $lockedAccount = $this->activeLockedAccount($account);
            $normalizedAmount = $this->normalizeAmount($amount);
            $balanceBefore = $this->normalizeAmount($lockedAccount->balance);

            if ($this->toCents($normalizedAmount) > $this->toCents($balanceBefore)) {
                throw ValidationException::withMessages([
                    'amount' => 'The withdrawal amount cannot be greater than your available balance.',
                ]);
            }

            $balanceAfter = $this->subtract($balanceBefore, $normalizedAmount);

            $lockedAccount->update([
                'balance' => $balanceAfter,
            ]);

            return $this->recordTransaction(
                $lockedAccount,
                Transaction::TYPE_CUSTOMER_WITHDRAWAL,
                $normalizedAmount,
                $balanceBefore,
                $balanceAfter,
                'Customer dashboard withdrawal.',
            );
        });
    }

    private function activeLockedAccount(Account $account): Account
    {
        $lockedAccount = Account::query()
            ->whereKey($account->id)
            ->lockForUpdate()
            ->firstOrFail();

        if (! $lockedAccount->isActive()) {
            throw ValidationException::withMessages([
                'account' => 'Only active accounts can deposit or withdraw money.',
            ]);
        }

        return $lockedAccount;
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
            'source' => Transaction::SOURCE_CUSTOMER,
            'description' => $description,
        ]);
    }

    private function generateReference(): string
    {
        do {
            $reference = 'CUST'.now()->format('ymdHis').random_int(1000, 9999);
        } while (Transaction::query()->where('reference', $reference)->exists());

        return $reference;
    }

    private function add(string $left, string $right): string
    {
        return number_format(((float) $left) + ((float) $right), 2, '.', '');
    }

    private function subtract(string $left, string $right): string
    {
        return number_format(((float) $left) - ((float) $right), 2, '.', '');
    }

    private function normalizeAmount(string|float|int $amount): string
    {
        return number_format((float) $amount, 2, '.', '');
    }

    private function toCents(string|float|int $amount): int
    {
        return (int) str_replace('.', '', $this->normalizeAmount($amount));
    }
}
