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
            ['email' => 'admin@centralbank.test'],
            [
                'name' => 'Master Admin',
                'phone' => '+8801700000000',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_APPROVED,
                'employee_code' => 'ADM-0001',
            ],
        );
    }
}
