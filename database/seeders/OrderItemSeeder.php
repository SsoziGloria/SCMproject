<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::orderBy('id')->take(2)->get();
        $products = Product::orderBy('id')->take(2)->get();

        if ($orders->count() < 2 || $products->count() < 2) {
            $this->command->warn('Not enough orders or products to seed order items.');
            return;
        }

        // First order: 2 items
        OrderItem::create([
            'order_id' => $orders[0]->id,
            'product_id' => $products[0]->id,
            'quantity' => 3,
            'price' => $products[0]->price,
            'subtotal' => $products[0]->price * 3,
        ]);
        OrderItem::create([
            'order_id' => $orders[0]->id,
            'product_id' => $products[1]->id,
            'quantity' => 1,
            'price' => $products[1]->price,
            'subtotal' => $products[1]->price,
        ]);

        // Second order: 1 item
        OrderItem::create([
            'order_id' => $orders[1]->id,
            'product_id' => $products[1]->id,
            'quantity' => 5,
            'price' => $products[1]->price,
            'subtotal' => $products[1]->price * 5,
        ]);
    }
}