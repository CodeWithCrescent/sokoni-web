<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        // Get vendor IDs (users with vendor role)
        $vendorIds = Vendor::pluck('id')->toArray();
        
        return [
            'user_id' => $this->faker->randomElement($vendorIds),
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
            'name' => $this->faker->word(),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'stock_quantity' => $this->faker->numberBetween(0, 100),
        ];
    }
}