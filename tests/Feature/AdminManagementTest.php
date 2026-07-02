<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_master_admin_can_access_management_pages(): void
    {
        $customer = User::factory()->create();
        $admin = User::factory()->masterAdmin()->create();

        $this->actingAs($customer)
            ->get(route('admin.branches.index'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.branches.index'))
            ->assertOk();
    }

    public function test_admin_can_search_paginated_branches(): void
    {
        $admin = User::factory()->masterAdmin()->create();

        Branch::factory()->create([
            'name' => 'Mirpur Branch',
            'city' => 'Dhaka',
        ]);

        Branch::factory()->create([
            'name' => 'Uttara Branch',
            'city' => 'Dhaka',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.branches.index', ['search' => 'Mirpur']));

        $response->assertOk();
        $response->assertSee('Mirpur Branch');
        $response->assertDontSee('Uttara Branch');
    }

    public function test_admin_can_create_branch_and_assign_employee(): void
    {
        $admin = User::factory()->masterAdmin()->create();
        $employee = User::factory()->employee()->create();

        $response = $this->actingAs($admin)
            ->post(route('admin.branches.store'), [
                'name' => 'Gulshan Branch',
                'branch_code' => 'GLS001',
                'city' => 'Dhaka',
                'address' => 'Gulshan Avenue',
                'country_code' => 'BD',
                'is_active' => '1',
                'employee_ids' => [$employee->id],
            ]);

        $branch = Branch::where('branch_code', 'GLS001')->firstOrFail();

        $response->assertRedirect(route('admin.branches.show', $branch));
        $this->assertTrue($branch->employees()->whereKey($employee->id)->exists());
    }

    public function test_admin_can_create_employee_and_assign_branch(): void
    {
        $admin = User::factory()->masterAdmin()->create();
        $branch = Branch::factory()->create();

        $response = $this->actingAs($admin)
            ->post(route('admin.employees.store'), [
                'name' => 'New Employee',
                'email' => 'new.employee@example.com',
                'phone' => '+8801700000002',
                'employee_code' => 'EMP-9001',
                'password' => 'password',
                'password_confirmation' => 'password',
                'status' => User::STATUS_APPROVED,
                'branch_ids' => [$branch->id],
            ]);

        $employee = User::where('email', 'new.employee@example.com')->firstOrFail();

        $response->assertRedirect(route('admin.employees.show', $employee));
        $this->assertTrue($employee->isEmployee());
        $this->assertTrue($employee->branches()->whereKey($branch->id)->exists());
        $this->assertDatabaseHas('branch_employee', [
            'branch_id' => $branch->id,
            'employee_id' => $employee->id,
        ]);
    }

    public function test_confirmation_pages_render_for_branch_and_employee_deletes(): void
    {
        $admin = User::factory()->masterAdmin()->create();
        $branch = Branch::factory()->create();
        $employee = User::factory()->employee()->create();

        $this->actingAs($admin)
            ->get(route('admin.branches.confirm-destroy', $branch))
            ->assertOk()
            ->assertSee('Delete '.$branch->name);

        $this->actingAs($admin)
            ->get(route('admin.employees.confirm-destroy', $employee))
            ->assertOk()
            ->assertSee('Delete '.$employee->name);
    }
}
