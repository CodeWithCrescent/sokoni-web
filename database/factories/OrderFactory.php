<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\DeliveryPersonnel;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        // Get customer IDs
        $customerIds = Customer::pluck('id')->toArray();
        
        // Get delivery personnel IDs
        $deliveryIds = DeliveryPersonnel::pluck('id')->toArray();
        $deliveryId = $this->faker->boolean(80) ? $this->faker->randomElement($deliveryIds) : null;
        
        return [
            'customer_id' => $this->faker->randomElement($customerIds),
            'delivery_id' => $deliveryId,
            'order_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'status' => $this->faker->randomElement(['pending', 'processing', 'delivered', 'cancelled']),
            'total_amount' => $this->faker->randomFloat(2, 50, 5000),
        ];
    }
}