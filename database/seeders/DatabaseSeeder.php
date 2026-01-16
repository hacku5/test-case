<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Customers
        $customers = \App\Models\Customer::factory(5)->create();

        // 2. Create Products
        $products = \App\Models\Product::factory(10)->create([
            'stock_quantity' => 100,
            'is_active' => true,
        ]);

        // 3. Create Orders via Service (to ensure business logic)
        $orderService = app(\App\Services\OrderService::class);

        foreach ($customers as $customer) {
            // Create 1-2 orders for each customer
            $orderCount = rand(1, 2);
            for ($i = 0; $i < $orderCount; $i++) {
                // Select 1-3 random products
                $randomProducts = $products->random(rand(1, 3));
                $items = [];

                foreach ($randomProducts as $product) {
                    $items[] = [
                        'product_id' => $product->id,
                        'quantity' => rand(1, 3)
                    ];
                }

                try {
                    $orderService->createOrder(new \App\DTOs\Order\CreateOrderDTO(
                        customerId: $customer->id,
                        items: $items
                    ));
                } catch (\Exception $e) {
                    // Ignore limits or stock errors during seed
                    logger()->warning("Seed warning: " . $e->getMessage());
                }
            }
        }
    }
}
