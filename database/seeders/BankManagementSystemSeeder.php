<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Branch;
use App\Models\EmployeeAction;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BankManagementSystemSeeder extends Seeder
{
    public function run(): void
    {
        $mainBranch = Branch::firstOrCreate(
            ['branch_code' => 'CB001'],
            [
                'name' => 'Central Branch',
                'address' => '100 Motijheel Commercial Area',
                'city' => 'Dhaka',
                'country_code' => 'BD',
                'is_active' => true,
            ],
        );

        $employee = User::updateOrCreate(
            ['email' => 'e1@centralbank.com'],
            [
                'name' => 'Employee 1',
                'phone' => '+8801722222222',
                'password' => Hash::make('password'),
                'role' => User::ROLE_EMPLOYEE,
                'status' => User::STATUS_APPROVED,
                'employee_code' => 'EMP-1001',
            ],
        );

        $customer = User::updateOrCreate(
            ['email' => 'customer@centralbank.com'],
            [
                'name' => 'Customer 1',
                'phone' => '+8801733333333',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CUSTOMER,
                'status' => User::STATUS_APPROVED,
                'employee_code' => null,
            ],
        );

        User::updateOrCreate(
            ['email' => 'pending@centralbank.com'],
            [
                'name' => 'Pending Customer',
                'phone' => '+8801744444444',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CUSTOMER,
                'status' => User::STATUS_PENDING,
                'employee_code' => null,
            ],
        );

        $mainBranch->employees()->syncWithoutDetaching([
            $employee->id => [
                'assigned_at' => now(),
            ],
        ]);

        $account = Account::firstOrCreate(
            ['account_number' => '100000000001'],
            [
                'user_id' => $customer->id,
                'branch_id' => $mainBranch->id,
                'account_type' => Account::TYPE_SAVINGS,
                'balance' => 25000,
                'status' => Account::STATUS_ACTIVE,
                'approved_by' => $employee->id,
                'approved_at' => now(),
            ],
        );

        Transaction::firstOrCreate(
            ['reference' => 'TXN0000000001'],
            [
                'account_id' => $account->id,
                'type' => Transaction::TYPE_ADJUSTMENT,
                'amount' => 25000,
                'balance_before' => 0,
                'balance_after' => 25000,
                'status' => Transaction::STATUS_COMPLETED,
                'source' => Transaction::SOURCE_SYSTEM,
                'description' => 'Opening balance deposit',
                'handled_by' => $employee->id,
            ],
        );

        EmployeeAction::firstOrCreate(
            [
                'employee_id' => $employee->id,
                'subject_type' => User::class,
                'subject_id' => $customer->id,
                'action_type' => EmployeeAction::TYPE_CUSTOMER_APPROVED,
            ],
            [
                'description' => 'Seeded approved customer for local development.',
                'metadata' => [
                    'seeded' => true,
                    'branch_id' => $mainBranch->id,
                    'account_id' => $account->id,
                ],
            ],
        );
    }
}
