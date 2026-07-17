<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_filter_and_view_own_transaction_history(): void
    {
        $customer = User::factory()->approvedCustomer()->create();
        $account = Account::factory()->create([
            'user_id' => $customer->id,
            'account_number' => '101234567890',
        ]);

        $deposit = Transaction::factory()->create([
            'account_id' => $account->id,
            'reference' => 'CUST-HISTORY-1',
            'type' => Transaction::TYPE_CUSTOMER_DEPOSIT,
            'status' => Transaction::STATUS_COMPLETED,
            'source' => Transaction::SOURCE_CUSTOMER,
            'amount' => '500.00',
        ]);

        Transaction::factory()->create([
            'account_id' => $account->id,
            'reference' => 'ATM-HISTORY-1',
            'type' => Transaction::TYPE_ATM_WITHDRAWAL,
            'status' => Transaction::STATUS_COMPLETED,
            'source' => Transaction::SOURCE_ATM,
        ]);

        $this
            ->actingAs($customer)
            ->get(route('customer.account.transactions', ['type' => Transaction::TYPE_CUSTOMER_DEPOSIT]))
            ->assertOk()
            ->assertSee('CUST-HISTORY-1')
            ->assertDontSee('ATM-HISTORY-1');

        $this
            ->actingAs($customer)
            ->get(route('customer.account.transactions.show', $deposit))
            ->assertOk()
            ->assertSee('CUST-HISTORY-1')
            ->assertSee('Customer deposit');
    }

    public function test_customer_cannot_view_another_customer_transaction(): void
    {
        $customer = User::factory()->approvedCustomer()->create();
        $account = Account::factory()->create(['user_id' => $customer->id]);
        $otherAccount = Account::factory()->create();

        Transaction::factory()->create([
            'account_id' => $account->id,
        ]);
        $otherTransaction = Transaction::factory()->create([
            'account_id' => $otherAccount->id,
            'reference' => 'PRIVATE-TXN',
        ]);

        $this
            ->actingAs($customer)
            ->get(route('customer.account.transactions.show', $otherTransaction))
            ->assertNotFound();
    }

    public function test_employee_can_search_transactions_by_customer_account_and_status(): void
    {
        $employee = User::factory()->employee()->create();
        $customer = User::factory()->approvedCustomer()->create([
            'name' => 'Nadia Rahman',
            'email' => 'nadia@example.test',
        ]);
        $account = Account::factory()->create([
            'user_id' => $customer->id,
            'account_number' => '109999888877',
        ]);

        $transaction = Transaction::factory()->create([
            'account_id' => $account->id,
            'reference' => 'EMP-SEARCH-1',
            'status' => Transaction::STATUS_COMPLETED,
            'source' => Transaction::SOURCE_TRANSFER,
            'type' => Transaction::TYPE_TRANSFER_CREDIT,
        ]);

        Transaction::factory()->create([
            'reference' => 'EMP-HIDDEN-1',
            'status' => Transaction::STATUS_PENDING,
        ]);

        $this
            ->actingAs($employee)
            ->get(route('employee.transactions.index', [
                'search' => 'Nadia',
                'status' => Transaction::STATUS_COMPLETED,
            ]))
            ->assertOk()
            ->assertSee('EMP-SEARCH-1')
            ->assertSee('109999888877')
            ->assertDontSee('EMP-HIDDEN-1');

        $this
            ->actingAs($employee)
            ->get(route('employee.transactions.show', $transaction))
            ->assertOk()
            ->assertSee('EMP-SEARCH-1')
            ->assertSee('Nadia Rahman')
            ->assertSee('Transfer credit');
    }

    public function test_employee_account_details_include_customer_transaction_history(): void
    {
        $employee = User::factory()->employee()->create();
        $customer = User::factory()->approvedCustomer()->create();
        $account = Account::factory()->create([
            'user_id' => $customer->id,
        ]);

        Transaction::factory()->create([
            'account_id' => $account->id,
            'reference' => 'ACCOUNT-HISTORY-1',
        ]);

        $this
            ->actingAs($employee)
            ->get(route('employee.accounts.show', $account))
            ->assertOk()
            ->assertSee('Customer history')
            ->assertSee('ACCOUNT-HISTORY-1');
    }
}
