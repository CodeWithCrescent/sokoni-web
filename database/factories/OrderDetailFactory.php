<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderDetailFactory extends Factory
{
    protected $model = OrderDetail::class;

    public function definition(): array
    {
        $product = Product::inRandomOrder()->first();
        
        return [
            'order_id' => Order::factory(),
            'product_id' => $product->id,
            'quantity' => $this->faker->numberBetween(1, 5),
            'price' => $product->price,
        ];
    }
}