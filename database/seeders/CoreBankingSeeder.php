<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Branch;
use App\Models\EmployeeAction;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CoreBankingSeeder extends Seeder
{
    public function run(): void
    {
        $mainBranch = Branch::firstOrCreate(
            ['code' => 'CB001'],
            [
                'name' => 'Central Branch',
                'city' => 'Dhaka',
                'address' => '100 Motijheel Commercial Area',
                'phone' => '+8801711111111',
                'is_active' => true,
            ],
        );

        $employee = User::updateOrCreate(
            ['email' => 'employee@centralbank.test'],
            [
                'name' => 'Branch Employee',
                'phone' => '+8801722222222',
                'password' => Hash::make('password'),
                'role' => User::ROLE_EMPLOYEE,
                'status' => User::STATUS_APPROVED,
                'employee_code' => 'EMP-1001',
            ],
        );

        $customer = User::updateOrCreate(
            ['email' => 'customer@centralbank.test'],
            [
                'name' => 'Approved Customer',
                'phone' => '+8801733333333',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CUSTOMER,
                'status' => User::STATUS_APPROVED,
                'employee_code' => null,
            ],
        );

        $mainBranch->employees()->syncWithoutDetaching([
            $employee->id => [
                'position' => 'Customer Service Officer',
                'assigned_at' => now(),
            ],
        ]);

        $account = Account::firstOrCreate(
            ['account_number' => '100000000001'],
            [
                'customer_id' => $customer->id,
                'branch_id' => $mainBranch->id,
                'type' => Account::TYPE_SAVINGS,
                'status' => Account::STATUS_ACTIVE,
                'balance' => 25000,
            ],
        );

        Transaction::firstOrCreate(
            ['transaction_number' => 'TXN0000000001'],
            [
                'account_id' => $account->id,
                'performed_by' => $employee->id,
                'type' => Transaction::TYPE_DEPOSIT,
                'amount' => 25000,
                'balance_after' => 25000,
                'reference' => 'OPENING-BALANCE',
                'description' => 'Opening balance deposit',
                'occurred_at' => now(),
            ],
        );

        EmployeeAction::firstOrCreate(
            [
                'employee_id' => $employee->id,
                'subject_user_id' => $customer->id,
                'action_type' => EmployeeAction::TYPE_CUSTOMER_APPROVED,
            ],
            [
                'branch_id' => $mainBranch->id,
                'description' => 'Seeded approved customer for local development.',
                'metadata' => ['seeded' => true],
            ],
        );
    }
}
