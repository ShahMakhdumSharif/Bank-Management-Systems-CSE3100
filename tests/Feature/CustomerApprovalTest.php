<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Branch;
use App\Models\EmployeeAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_view_pending_customer_queue(): void
    {
        $employee = User::factory()->employee()->create();
        $pendingCustomer = User::factory()->create([
            'name' => 'Pending Customer',
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_PENDING,
        ]);
        $approvedCustomer = User::factory()->approvedCustomer()->create([
            'name' => 'Approved Customer',
        ]);

        $response = $this->actingAs($employee)
            ->get(route('employee.customers.pending'));

        $response->assertOk();
        $response->assertSee($pendingCustomer->name);
        $response->assertDontSee($approvedCustomer->name);
    }

    public function test_non_employee_cannot_view_pending_customer_queue(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get(route('employee.customers.pending'))
            ->assertForbidden();
    }

    public function test_employee_can_approve_customer_and_create_account(): void
    {
        $employee = User::factory()->employee()->create();
        $customer = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_PENDING,
        ]);
        $branch = Branch::factory()->create();

        $response = $this->actingAs($employee)
            ->post(route('employee.customers.approve', $customer), [
                'branch_id' => $branch->id,
                'account_type' => Account::TYPE_SAVINGS,
            ]);

        $response->assertRedirect(route('employee.customers.show', $customer));

        $customer->refresh();
        $account = $customer->accounts()->firstOrFail();

        $this->assertSame(User::STATUS_APPROVED, $customer->status);
        $this->assertSame($branch->id, $account->branch_id);
        $this->assertSame(Account::TYPE_SAVINGS, $account->account_type);
        $this->assertSame(Account::STATUS_ACTIVE, $account->status);
        $this->assertNotEmpty($account->account_number);

        $this->assertDatabaseHas('employee_actions', [
            'employee_id' => $employee->id,
            'subject_type' => User::class,
            'subject_id' => $customer->id,
            'action_type' => EmployeeAction::TYPE_CUSTOMER_APPROVED,
        ]);
    }

    public function test_employee_can_reject_customer_and_record_reason(): void
    {
        $employee = User::factory()->employee()->create();
        $customer = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_PENDING,
        ]);

        $response = $this->actingAs($employee)
            ->post(route('employee.customers.reject', $customer), [
                'rejection_reason' => 'Identity document was not readable.',
            ]);

        $response->assertRedirect(route('employee.customers.show', $customer));

        $customer->refresh();

        $this->assertSame(User::STATUS_REJECTED, $customer->status);
        $this->assertFalse($customer->accounts()->exists());
        $this->assertDatabaseHas('employee_actions', [
            'employee_id' => $employee->id,
            'subject_type' => User::class,
            'subject_id' => $customer->id,
            'action_type' => EmployeeAction::TYPE_CUSTOMER_REJECTED,
            'description' => 'Identity document was not readable.',
        ]);
    }
}
