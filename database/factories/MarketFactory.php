<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Market;
use Illuminate\Database\Eloquent\Factories\Factory;

class MarketFactory extends Factory
{
    protected $model = Market::class;

    public function definition(): array
    {
        return [
            'id' => User::factory()->vendor(),
            'address' => $this->faker->address(),
        ];
    }
}