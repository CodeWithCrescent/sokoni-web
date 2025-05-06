<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\DeliveryPersonnel;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles
        $roles = ['customer', 'vendor', 'delivery', 'admin'];
        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        // Create users for each role
        // Customers
        $customerUsers = User::factory()->count(4)->create([
            'role_id' => Role::where('name', 'customer')->first()->id,
        ]);
        
        foreach ($customerUsers as $user) {
            Customer::factory()->create([
                'id' => $user->id,
            ]);
        }
        
        // Vendors
        $vendorUsers = User::factory()->count(3)->create([
            'role_id' => Role::where('name', 'vendor')->first()->id,
        ]);
        
        foreach ($vendorUsers as $user) {
            Vendor::factory()->create([
                'id' => $user->id,
            ]);
        }
        
        // Delivery Personnel
        $deliveryUsers = User::factory()->count(3)->create([
            'role_id' => Role::where('name', 'delivery')->first()->id,
        ]);
        
        foreach ($deliveryUsers as $user) {
            DeliveryPersonnel::factory()->create([
                'id' => $user->id,
            ]);
        }
        
        // Create categories
        Category::factory(4)->create();
        
        // Create products for each vendor
        $vendors = Vendor::all();
        $categories = Category::all();
        
        foreach ($vendors as $vendor) {
            Product::factory()->count(3)->create([
                'user_id' => $vendor->id,
                'category_id' => $categories->random()->id,
            ]);
        }
        
        // Create orders for customers
        $customers = Customer::all();
        $deliveryPersonnel = DeliveryPersonnel::all();
        $products = Product::all();
        
        foreach ($customers as $customer) {
            $order = Order::factory()->create([
                'customer_id' => $customer->id,
                'delivery_id' => $deliveryPersonnel->random()->id,
            ]);
            
            // Create 2-3 order details for each order
            for ($i = 0; $i < rand(2, 3); $i++) {
                $product = $products->random();
                $quantity = rand(1, 3);
                
                OrderDetail::factory()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);
            }
            
            // Update order total
            $total = OrderDetail::where('order_id', $order->id)
                ->selectRaw('SUM(price * quantity) as total')
                ->first()
                ->total;
                
            $order->update(['total_amount' => $total]);
        }
    }
}