<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Customer;

class UserSeeder extends Seeder
{
    public function run(): void
{
    $admin = User::updateOrCreate(
        ['email' => 'admin@gmail.com'],
        [
            'nama' => 'admin',
            'role' => '0',
            'status' => 1,
            'password' => Hash::make('admin123'),
            'hp' => '081234567890',
            'foto' => null,
        ]
    );

    $azizah = User::updateOrCreate(
        ['email' => 'azizah@gmail.com'],
        [
            'nama' => 'azizah',
            'role' => '2',
            'status' => 1,
            'password' => Hash::make('azizah123'),
            'hp' => '081298765432',
            'foto' => null,
        ]
    );

    Customer::updateOrCreate(
        ['user_id' => $azizah->id],
        [
            'google_id' => null,
            'google_token' => null,
            'alamat' => null,
            'pos' => null,
        ]
    );
}
}