<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'phone_number' => $this->faker->phoneNumber(),
            'password' => Hash::make('password'), // Default password for testing
            'role_id' => Role::inRandomOrder()->first()->id ?? Role::factory(),
            'remember_token' => Str::random(10),
        ];
    }

    public function customer(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role_id' => Role::where('name', 'customer')->first()->id,
            ];
        });
    }

    public function vendor(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role_id' => Role::where('name', 'vendor')->first()->id,
            ];
        });
    }

    public function delivery(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role_id' => Role::where('name', 'delivery')->first()->id,
            ];
        });
    }
}