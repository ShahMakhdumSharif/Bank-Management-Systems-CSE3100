<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Branch;
use App\Models\EmployeeAction;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\MasterAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankManagementSystemModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_branch_employee_account_transaction_and_action_relationships(): void
    {
        $branch = Branch::factory()->create();
        $employee = User::factory()->employee()->create();
        $customer = User::factory()->approvedCustomer()->create();

        $branch->employees()->attach($employee, [
            'assigned_at' => now(),
        ]);

        $account = Account::factory()->for($branch)->for($customer, 'customer')->create([
            'balance' => 1200,
        ]);

        $transaction = Transaction::factory()
            ->for($account)
            ->for($employee, 'handler')
            ->create([
                'type' => Transaction::TYPE_ADJUSTMENT,
                'amount' => 1200,
                'balance_before' => 0,
                'balance_after' => 1200,
            ]);

        $action = EmployeeAction::create([
            'employee_id' => $employee->id,
            'action_type' => EmployeeAction::TYPE_CUSTOMER_APPROVED,
            'subject_type' => User::class,
            'subject_id' => $customer->id,
            'description' => 'Approved customer.',
            'metadata' => ['branch_id' => $branch->id],
        ]);

        $this->assertTrue($branch->employees->contains($employee));
        $this->assertTrue($customer->accounts->contains($account));
        $this->assertTrue($account->transactions->contains($transaction));
        $this->assertTrue($employee->performedTransactions->contains($transaction));
        $this->assertTrue($employee->employeeActions->contains($action));
        $this->assertTrue($customer->subjectActions->contains($action));
    }

    public function test_master_admin_seeder_creates_approved_admin_user(): void
    {
        $this->seed(MasterAdminSeeder::class);

        $admin = User::where('email', 'admin@centralbank.com')->firstOrFail();

        $this->assertTrue($admin->isMasterAdmin());
        $this->assertSame(User::STATUS_APPROVED, $admin->status);
        $this->assertSame('ADM-0001', $admin->employee_code);
    }
}
