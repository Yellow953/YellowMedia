<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'joe@yellowbrand.com'],
            [
                'name' => 'Joe',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'tenant_id' => null,
            ]
        );
    }
}
