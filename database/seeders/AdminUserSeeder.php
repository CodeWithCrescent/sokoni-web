<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();

        if ($adminRole) {
            User::updateOrCreate(
                ['email' => 'admin@agizasokoni.com'],
                [
                    'role_id' => $adminRole->id,
                    'name' => 'System Administrator',
                    'email' => 'admin@agizasokoni.com',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
