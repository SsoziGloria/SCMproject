<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Helpers\LocationHelper;

class CheckoutController extends Controller
{
    /**
     * Display the checkout page
     */
    public function index()
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        $products = [];
        $total = 0;

        foreach ($cart as $id => $details) {
            $product = Product::find($id);
            if ($product) {
                // Check product is still in stock
                if ($product->stock < $details['quantity']) {
                    return redirect()->route('cart.index')
                        ->with('error', 'Some products in your cart are no longer available in the requested quantity.');
                }

                $products[$id] = [
                    'product' => $product,
                    'quantity' => $details['quantity']
                ];
                $total += $product->price * $details['quantity'];
            }
        }

        return view('checkout.index', compact('products', 'total'));
    }

    /**
     * Process the checkout
     */
    // In the process method of CheckoutController, update to include new fields:

    public function process(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:100',
            'shipping_country' => 'required|string|max:2',
            'shipping_method' => 'required|in:standard,express,overnight',
            'payment' => 'required|in:credit_card,mobile_money,bank_transfer',
            'terms' => 'required|accepted',
            'total_amount' => 'required|numeric',
        ]);

        // Validate cart has items
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        // Start database transaction
        DB::beginTransaction();

        try {
            // Calculate shipping fee based on method
            $shippingFee = 0;
            if ($validated['shipping_method'] === 'express') {
                $shippingFee = 3000;
            } elseif ($validated['shipping_method'] === 'overnight') {
                $shippingFee = 10000;
            }

            // Calculate subtotal
            $subtotal = 0;
            foreach ($cart as $productId => $details) {
                $product = Product::find($productId);
                if ($product) {
                    $subtotal += $product->price * $details['quantity'];
                }
            }

            // Generate order number
            $orderNumber = 'ORD-' . date('Y') . '-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);

            $region = LocationHelper::getRegionFromCity($validated['shipping_city']);
            $country = LocationHelper::getCountryFromCity($validated['shipping_city']);

            // Create the order with new fields
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => Auth::check() ? Auth::id() : null,
                'phone' => $validated['phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_country' => $validated['shipping_country'],
                'shipping_region' => $region,
                'shipping_fee' => $shippingFee,
                'subtotal' => $subtotal,
                'total_amount' => $validated['total_amount'],
                'discount_amount' => 0,
                'status' => 'pending',
                'payment' => $validated['payment'],
                'notes' => $request->input('notes'),
                'sales_channel' => 'online', // Since this is from the website
                'sales_channel_id' => 1,
            ]);

            // Create order items with enhanced fields
            foreach ($cart as $productId => $details) {
                $product = Product::find($productId);

                if (!$product || $product->stock < $details['quantity']) {
                    throw new \Exception('Product not available in requested quantity.');
                }

                // Create order item with enhanced fields
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'product_name' => $product->name,
                    'product_category' => $product->category,
                    'quantity' => $details['quantity'],
                    'price' => $product->price,
                    'unit_cost' => $product->cost,
                    'subtotal' => $product->price * $details['quantity'],
                ]);

                // Update product stock
                $product->stock -= $details['quantity'];
                $product->save();
            }

            // Clear the cart
            Session::forget('cart');

            // Commit transaction
            DB::commit();

            // Redirect to order confirmation
            return redirect()->route('checkout.confirmation', ['order' => $order->id]);

        } catch (\Exception $e) {
            // Roll back transaction on error
            DB::rollBack();
            return redirect()->back()->with('error', 'Error processing your order: ' . $e->getMessage());
        }
    }

    /**
     * Display order confirmation
     */
    public function confirmation($orderId)
    {
        $order = Order::with(['items.product'])->findOrFail($orderId);

        // Security check - only allow viewing your own orders
        if (Auth::check() && Auth::id() !== $order->user_id && !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('checkout.confirmation', compact('order'));
    }
}