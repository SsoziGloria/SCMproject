<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\CustomerSegment;
use App\Models\DemandPrediction;
use App\Models\Inventory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ComprehensiveBusinessDataSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->command->info('Clearing existing data...');
        CustomerSegment::truncate();
        DemandPrediction::truncate();
        OrderItem::truncate();
        Order::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $products = Product::all();
        if ($products->isEmpty()) {
            $this->command->error('No products found. Please run product seeder first.');
            return;
        }

        $this->command->info('Creating realistic orders...');
        $this->createRealisticOrders($products);

        $this->command->info('Creating customer segments...');
        $this->createCustomerSegments();

        // Generate demand predictions for products
        $this->command->info('Creating demand predictions...');
        $this->createDemandPredictions($products);

        // Update inventory levels
        $this->command->info('Updating inventory levels...');
        $this->updateInventoryLevels($products);

        $this->command->info('Business data seeding completed successfully!');
    }

    private function createRealisticOrders($products)
    {
        $userIds = \App\Models\User::pluck('id')->toArray();
        if (empty($userIds)) {
            $this->command->error('No users found. Please create users first.');
            return;
        }

        $startDate = Carbon::now()->subMonths(3);
        $endDate = Carbon::now();

        $orderStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        $statusWeights = [5, 10, 15, 65, 5];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $ordersPerDay = rand(3, 7);

            for ($i = 0; $i < $ordersPerDay; $i++) {
                $userId = $userIds[array_rand($userIds)];
                $status = $this->weightedRandomChoice($orderStatuses, $statusWeights);

                $order = Order::create([
                    'user_id' => $userId,
                    'status' => $status,
                    'payment' => 'mobile_money',
                    'total_amount' => 0,
                    'subtotal' => 0,
                    'shipping_fee' => rand(0, 3000),
                    'shipping_address' => 'Sample Address ' . $userId,
                    'shipping_city' => 'Kampala',
                    'shipping_region' => 'Central',
                    'shipping_country' => 'Uganda',
                    'payment_status' => $status === 'delivered' ? 'paid' : 'pending',
                    'order_date' => $date->copy()->addHours(rand(8, 22))->addMinutes(rand(0, 59)),
                    'created_at' => $date->copy()->addHours(rand(8, 22))->addMinutes(rand(0, 59)),
                    'updated_at' => $date->copy()->addHours(rand(8, 22))->addMinutes(rand(0, 59))
                ]);

                $itemCount = rand(1, 4);
                $subtotal = 0;

                for ($j = 0; $j < $itemCount; $j++) {
                    $product = $products->random();
                    $quantity = rand(1, 5);
                    $unitPrice = $product->price;
                    $lineTotal = $quantity * $unitPrice;
                    $subtotal += $lineTotal;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_category' => $product->category ?? 'Chocolate',
                        'quantity' => $quantity,
                        'price' => $unitPrice,
                        'unit_cost' => $product->cost ?? ($unitPrice * 0.6),
                        'subtotal' => $lineTotal,
                        'created_at' => $order->created_at,
                        'updated_at' => $order->updated_at
                    ]);
                }

                $totalAmount = $subtotal + $order->shipping_fee;
                $order->update([
                    'subtotal' => $subtotal,
                    'total_amount' => $totalAmount
                ]);
            }
        }

        $this->command->info('Created ' . Order::count() . ' orders with ' . OrderItem::count() . ' order items');
    }

    private function createCustomerSegments()
    {
        CustomerSegment::truncate();

        $customerData = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'delivered')
            ->select('orders.user_id as customer_id')
            ->selectRaw('SUM(order_items.quantity) as total_quantity')
            ->selectRaw('SUM(order_items.subtotal) as total_amount')
            ->selectRaw('COUNT(DISTINCT orders.id) as purchase_count')
            ->groupBy('orders.user_id')
            ->having('total_quantity', '>', 0)
            ->get();

        foreach ($customerData as $data) {
            $cluster = $this->determineCluster($data->total_quantity, $data->total_amount, $data->purchase_count);

            CustomerSegment::create([
                'customer_id' => $data->customer_id,
                'quantity' => $data->total_quantity,
                'total_quantity' => $data->total_quantity,
                'purchase_count' => $data->purchase_count,
                'cluster' => $cluster,
                'created_at' => Carbon::now()->subDays(rand(1, 30))
            ]);
        }

        $this->command->info('Created ' . CustomerSegment::count() . ' customer segments');
    }

    private function determineCluster($totalQuantity, $totalAmount, $purchaseCount)
    {
        // High value customers
        if ($totalAmount > 500000 && $purchaseCount > 10) {
            return 1;
        }
        // Medium value customers
        elseif ($totalAmount > 200000 && $purchaseCount > 5) {
            return 2;
        }
        // Low engagement but high value orders
        elseif ($totalAmount > 300000) {
            return 3;
        }
        // New or low value customers
        else {
            return 4;
        }
    }

    private function createDemandPredictions($products)
    {
        DemandPrediction::truncate();

        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now()->addDays(60);

        foreach ($products as $product) {
            // Calculate historical average demand for this product
            $historicalDemand = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('order_items.product_id', $product->id)
                ->where('orders.status', 'delivered')
                ->where('orders.created_at', '>=', Carbon::now()->subDays(90))
                ->avg('order_items.quantity') ?? 5;

            // Add some randomness to predictions
            $baseDemand = max(1, $historicalDemand);

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                // Add seasonal/weekly variations
                $dayOfWeek = $date->dayOfWeek;
                $weekendMultiplier = in_array($dayOfWeek, [5, 6]) ? 1.3 : 1.0; // Higher demand on weekends

                // Monthly trends (simulate seasonal patterns)
                $monthMultiplier = 1.0;
                if (in_array($date->month, [11, 12])) { // Holiday season
                    $monthMultiplier = 1.5;
                } elseif (in_array($date->month, [6, 7, 8])) { // Summer season
                    $monthMultiplier = 1.2;
                }

                // Random variation ±30%
                $randomMultiplier = rand(70, 130) / 100;

                $predictedQuantity = round($baseDemand * $weekendMultiplier * $monthMultiplier * $randomMultiplier);

                DemandPrediction::create([
                    'product_id' => $product->product_id, // Use product_id field, not id
                    'prediction_date' => $date->toDateString(),
                    'predicted_quantity' => max(1, $predictedQuantity)
                ]);
            }
        }

        $this->command->info('Created ' . DemandPrediction::count() . ' demand predictions');
    }

    private function updateInventoryLevels($products)
    {
        foreach ($products as $product) {
            // Calculate recommended stock level based on predictions
            $avgDailyDemand = DemandPrediction::where('product_id', $product->product_id)
                ->avg('predicted_quantity') ?? 10;

            // Set stock to 30 days of predicted demand + some buffer
            $recommendedStock = round($avgDailyDemand * 30 * 1.2);

            // Update product stock
            $product->update([
                'stock' => rand($recommendedStock * 0.5, $recommendedStock * 1.5)
            ]);

            // Update inventory if it exists
            $inventory = Inventory::where('product_id', $product->id)->first();
            if ($inventory) {
                $inventory->update([
                    'quantity' => $product->stock,
                    'reorder_level' => round($avgDailyDemand * 7) // 1 week supply
                ]);
            }
        }

        $this->command->info('Updated inventory levels for ' . $products->count() . ' products');
    }

    private function weightedRandomChoice($choices, $weights)
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);

        $currentWeight = 0;
        foreach ($choices as $index => $choice) {
            $currentWeight += $weights[$index];
            if ($random <= $currentWeight) {
                return $choice;
            }
        }

        return $choices[0]; // Fallback
    }
}
