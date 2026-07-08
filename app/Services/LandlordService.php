<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LandlordService
{
    public function create(array $data): array
    {
        return DB::transaction(function () use ($data) {

            $password = Str::password(10);

            $user = User::create([

                'name' => $data['name'],

                'username' => $data['username'],

                'email' => $data['email'],

                'phone' => $data['phone'],

                'second_phone' => $data['second_phone'] ?? null,

                'password' => Hash::make($password),

              'status' => 1,

            ]);

            $user->assignRole('Landlord');

            return [

                'username' => $user->username,

                'password' => $password,

            ];

        });
    }
}