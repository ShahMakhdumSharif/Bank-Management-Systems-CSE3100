<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MasterAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@centralbank.com'],
            [
                'name' => 'Shah Makhdum Sharif',
                'phone' => '+8801860026356',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_APPROVED,
                'employee_code' => 'ADM-0001',
            ],
        );
    }
}
