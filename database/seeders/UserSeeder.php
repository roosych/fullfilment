<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['phone' => '+994504801517'],
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        // Присвоение роли ADMIN через enum
        $admin->assignRole(UserRoleEnum::ADMIN->value);

        //User::factory()->count(5)->create();
    }
}
