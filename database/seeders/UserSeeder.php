<?php

namespace Database\Seeders;

use App\Enums\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Aleksandr',
            'role' => Role::ADMIN,
            'email' => 'sasha@mail.ru',
            'password' => Hash::make('12345678a!')
        ]);
    }
}