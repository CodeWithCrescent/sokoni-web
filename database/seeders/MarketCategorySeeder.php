<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class MarketCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $localMarketCategory = ProductCategory::updateOrCreate(
            ['slug' => 'local-market'],
            [
                'name' => 'Local Market',
                'description' => 'Default category for local markets',
                'is_active' => true,
            ]
        );

        $this->command->info('Local Market category created or already exists.');
    }
}
