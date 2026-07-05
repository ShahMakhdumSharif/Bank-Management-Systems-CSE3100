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
    }
}
