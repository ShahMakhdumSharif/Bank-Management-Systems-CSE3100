<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\EmployeeAction;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_master_admin_can_view_bank_analytics_and_employee_performance(): void
    {
        $admin = User::factory()->masterAdmin()->create();
        $employee = User::factory()->employee()->create([
            'name' => 'Ayesha Karim',
            'email' => 'ayesha@example.test',
        ]);

        $approvedCustomers = User::factory()->approvedCustomer()->count(2)->create();
        User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_PENDING,
        ]);
        User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_REJECTED,
        ]);

        $activeAccount = Account::factory()->create([
            'user_id' => $approvedCustomers[0]->id,
            'balance' => '1500.00',
        ]);
        Account::factory()->frozen()->create([
            'user_id' => $approvedCustomers[1]->id,
            'balance' => '250.00',
        ]);

        Transaction::factory()->create([
            'account_id' => $activeAccount->id,
            'type' => Transaction::TYPE_CUSTOMER_DEPOSIT,
            'status' => Transaction::STATUS_COMPLETED,
            'amount' => '1000.00',
            'handled_by' => null,
        ]);
        Transaction::factory()->create([
            'account_id' => $activeAccount->id,
            'type' => Transaction::TYPE_ATM_DEPOSIT,
            'status' => Transaction::STATUS_COMPLETED,
            'amount' => '500.00',
            'handled_by' => null,
        ]);
        Transaction::factory()->create([
            'account_id' => $activeAccount->id,
            'type' => Transaction::TYPE_ATM_WITHDRAWAL,
            'status' => Transaction::STATUS_COMPLETED,
            'amount' => '800.00',
            'handled_by' => null,
        ]);

        EmployeeAction::factory()->create([
            'employee_id' => $employee->id,
            'action_type' => EmployeeAction::TYPE_CUSTOMER_APPROVED,
            'subject_type' => null,
            'subject_id' => null,
        ]);
        EmployeeAction::factory()->create([
            'employee_id' => $employee->id,
            'action_type' => EmployeeAction::TYPE_TRANSFER_APPROVED,
            'subject_type' => null,
            'subject_id' => null,
        ]);
        EmployeeAction::factory()->create([
            'employee_id' => $employee->id,
            'action_type' => EmployeeAction::TYPE_ATM_CARD_BLOCKED,
            'subject_type' => null,
            'subject_id' => null,
        ]);
        EmployeeAction::factory()->create([
            'employee_id' => $employee->id,
            'action_type' => EmployeeAction::TYPE_ACCOUNT_FROZEN,
            'subject_type' => null,
            'subject_id' => null,
        ]);

        $this
            ->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Bank analytics')
            ->assertSee('Approved')
            ->assertSee('BDT 1,750.00')
            ->assertSee('BDT 1,500.00')
            ->assertSee('Ayesha Karim')
            ->assertSee('ayesha@example.test')
            ->assertSee('Processing activity');
    }

    public function test_non_admin_cannot_view_admin_analytics(): void
    {
        $customer = User::factory()->approvedCustomer()->create();

        $this
            ->actingAs($customer)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
