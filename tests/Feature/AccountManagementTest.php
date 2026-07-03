<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\EmployeeAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_search_customer_accounts(): void
    {
        $employee = User::factory()->employee()->create();
        $matchedAccount = Account::factory()
            ->for(User::factory()->approvedCustomer()->state(['name' => 'Nadia Rahman']), 'customer')
            ->create();
        $otherAccount = Account::factory()
            ->for(User::factory()->approvedCustomer()->state(['name' => 'Farid Khan']), 'customer')
            ->create();

        $response = $this->actingAs($employee)
            ->get(route('employee.accounts.index', ['search' => 'Nadia']));

        $response->assertOk();
        $response->assertSee($matchedAccount->account_number);
        $response->assertDontSee($otherAccount->account_number);
    }

    public function test_non_employee_cannot_view_account_management(): void
    {
        $customer = User::factory()->approvedCustomer()->create();

        $this->actingAs($customer)
            ->get(route('employee.accounts.index'))
            ->assertForbidden();
    }

    public function test_employee_can_freeze_account_with_reason(): void
    {
        $employee = User::factory()->employee()->create();
        $account = Account::factory()->create();

        $response = $this->actingAs($employee)
            ->post(route('employee.accounts.freeze', $account), [
                'freeze_reason' => 'Customer reported suspicious withdrawal activity.',
            ]);

        $response->assertRedirect(route('employee.accounts.show', $account));

        $account->refresh();

        $this->assertSame(Account::STATUS_FROZEN, $account->status);
        $this->assertSame($employee->id, $account->frozen_by);
        $this->assertSame('Customer reported suspicious withdrawal activity.', $account->freeze_reason);
        $this->assertNotNull($account->frozen_at);

        $this->assertDatabaseHas('employee_actions', [
            'employee_id' => $employee->id,
            'subject_type' => Account::class,
            'subject_id' => $account->id,
            'action_type' => EmployeeAction::TYPE_ACCOUNT_FROZEN,
            'description' => 'Customer reported suspicious withdrawal activity.',
        ]);
    }

    public function test_employee_can_unfreeze_account(): void
    {
        $employee = User::factory()->employee()->create();
        $account = Account::factory()->frozen()->create([
            'frozen_by' => $employee->id,
        ]);

        $response = $this->actingAs($employee)
            ->post(route('employee.accounts.unfreeze', $account));

        $response->assertRedirect(route('employee.accounts.show', $account));

        $account->refresh();

        $this->assertSame(Account::STATUS_ACTIVE, $account->status);
        $this->assertNull($account->frozen_by);
        $this->assertNull($account->frozen_at);
        $this->assertNull($account->freeze_reason);

        $this->assertDatabaseHas('employee_actions', [
            'employee_id' => $employee->id,
            'subject_type' => Account::class,
            'subject_id' => $account->id,
            'action_type' => EmployeeAction::TYPE_ACCOUNT_UNFROZEN,
        ]);
    }

    public function test_account_page_size_preference_is_saved_in_cookie(): void
    {
        $employee = User::factory()->employee()->create();

        $response = $this->actingAs($employee)
            ->get(route('employee.accounts.index', ['page_size' => 25]));

        $response->assertOk();
        $response->assertCookie('employee_account_page_size', '25');
    }
}
