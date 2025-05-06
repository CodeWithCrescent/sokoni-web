<?php

namespace Database\Factories;

use App\Models\DeliveryPersonnel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryPersonnelFactory extends Factory
{
    protected $model = DeliveryPersonnel::class;

    public function definition(): array
    {
        return [
            'id' => User::factory()->delivery(),
            'license_plate' => $this->faker->bothify('??###??'),
            'status' => $this->faker->randomElement(['available', 'on_delivery', 'off_duty']),
        ];
    }
}