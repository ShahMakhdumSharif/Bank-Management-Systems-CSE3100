<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_master_admin_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_APPROVED,
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_employee_is_redirected_to_employee_dashboard(): void
    {
        $employee = User::factory()->create([
            'role' => User::ROLE_EMPLOYEE,
            'status' => User::STATUS_APPROVED,
            'employee_code' => 'EMP-1001',
        ]);

        $response = $this->actingAs($employee)->get('/dashboard');

        $response->assertRedirect(route('employee.dashboard'));
    }

    public function test_customer_is_redirected_to_customer_dashboard(): void
    {
        $customer = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
        ]);

        $response = $this->actingAs($customer)->get('/dashboard');

        $response->assertRedirect(route('customer.dashboard'));
    }

    public function test_role_dashboards_reject_wrong_roles(): void
    {
        $customer = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
        ]);

        $this->actingAs($customer)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($customer)->get(route('employee.dashboard'))->assertForbidden();
        $this->actingAs($customer)->get(route('customer.dashboard'))->assertOk();
    }
}
