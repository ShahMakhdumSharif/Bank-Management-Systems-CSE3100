<?php

namespace Tests\Feature;

use App\Models\EmployeeAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_master_admin_can_filter_and_view_audit_logs(): void
    {
        $admin = User::factory()->masterAdmin()->create();
        $employee = User::factory()->employee()->create([
            'name' => 'Ayesha Karim',
            'email' => 'ayesha@example.test',
            'employee_code' => 'EMP-1001',
        ]);
        $otherEmployee = User::factory()->employee()->create([
            'name' => 'Tanvir Islam',
        ]);

        $auditLog = EmployeeAction::factory()->create([
            'employee_id' => $employee->id,
            'action_type' => EmployeeAction::TYPE_TRANSFER_APPROVED,
            'subject_type' => null,
            'subject_id' => null,
            'description' => 'Approved transfer request for payroll.',
            'metadata' => [
                'amount' => '1500.00',
                'sender_account_id' => 10,
                'receiver_account_id' => 11,
            ],
            'ip_address' => '127.0.0.1',
        ]);
        EmployeeAction::factory()->create([
            'employee_id' => $otherEmployee->id,
            'action_type' => EmployeeAction::TYPE_ACCOUNT_FROZEN,
            'subject_type' => null,
            'subject_id' => null,
            'description' => 'Hidden audit entry',
        ]);

        $this
            ->actingAs($admin)
            ->get(route('admin.audit-logs.index', [
                'search' => 'Ayesha',
                'action_type' => EmployeeAction::TYPE_TRANSFER_APPROVED,
            ]))
            ->assertOk()
            ->assertSee('Admin audit logs')
            ->assertSee('Ayesha Karim')
            ->assertSee('Transfer approved')
            ->assertDontSee('Hidden audit entry');

        $this
            ->actingAs($admin)
            ->get(route('admin.audit-logs.show', $auditLog))
            ->assertOk()
            ->assertSee('Approved transfer request for payroll.')
            ->assertSee('Amount')
            ->assertSee('1500.00')
            ->assertSee('127.0.0.1')
            ->assertSee('Record unavailable');
    }

    public function test_admin_dashboard_links_to_audit_logs(): void
    {
        $admin = User::factory()->masterAdmin()->create();

        $this
            ->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.audit-logs.index'))
            ->assertSee('View Audit Logs');
    }

    public function test_non_admin_cannot_view_audit_logs(): void
    {
        $customer = User::factory()->approvedCustomer()->create();
        $auditLog = EmployeeAction::factory()->create([
            'subject_type' => null,
            'subject_id' => null,
        ]);

        $this
            ->actingAs($customer)
            ->get(route('admin.audit-logs.index'))
            ->assertForbidden();

        $this
            ->actingAs($customer)
            ->get(route('admin.audit-logs.show', $auditLog))
            ->assertForbidden();
    }
}
