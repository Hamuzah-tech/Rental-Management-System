<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
       $admin = User::firstOrCreate(
    ['email' => 'admin@rms.com'],
    [
        'name' => 'Super Administrator',
        'username' => 'admin',
        'phone' => '0990000000',
        'second_phone' => null,
        'password' => Hash::make('password'),
        'status' => true,
    ]
);

        $admin->assignRole('Super Admin');
    }
}
