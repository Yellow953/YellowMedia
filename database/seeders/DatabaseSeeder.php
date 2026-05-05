<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'yellow-media'],
            [
                'name'      => 'Yellow Media',
                'plan'      => 'pro',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'joe@yellow-media.com'],
            [
                'name'      => 'Joe',
                'password'  => Hash::make('qwe123'),
                'role'      => 'super_admin',
                'tenant_id' => $tenant->id,
            ]
        );

        $this->call(BrandProfileSeeder::class);
    }
}
