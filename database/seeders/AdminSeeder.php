<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the admin role
        $adminRole = Role::where('name', 'admin')->first();
        
        if (!$adminRole) {
            $this->command->error('Admin role not found. Please run RoleSeeder first.');
            return;
        }
        
        // Check if admin already exists
        $existingAdmin = User::where('email', 'admin1@gmail.com')->first();
        
        if ($existingAdmin) {
            $this->command->info('Admin user already exists.');
            return;
        }
        
        // Create admin user
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('12345678'),
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
        ]);
        
        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin1@gmail.com');
        $this->command->info('Password: 12345678');
    }
}
