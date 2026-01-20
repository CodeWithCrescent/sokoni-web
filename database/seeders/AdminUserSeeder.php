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
                ['email' => 'admin@agizasokoni.co.tz'],
                [
                    'role_id' => $adminRole->id,
                    'name' => 'System Administrator',
                    'slug' => 'system-administrator',
                    'email' => 'admin@agizasokoni.co.tz',
                    'password' => Hash::make('password9900'),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
