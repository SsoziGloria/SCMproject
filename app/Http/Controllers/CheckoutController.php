<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

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
    public function process(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
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
            // Generate order number
            $orderNumber = 'ORD-' . strtoupper(uniqid());

            // Create the order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => Auth::check() ? Auth::id() : null,
                'phone' => $validated['phone'],
                'total_amount' => $validated['total_amount'],
                'status' => 'pending',
                'payment' => $validated['payment'],
                'payment_status' => $validated['payment'] === 'mobile_money' ? 'pending' : 'paid',
                'shipping_address' => $validated['address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_country' => $validated['shipping_country'],
                'notes' => $request->input('notes'),
            ]);

            // Create order items and update stock
            foreach ($cart as $productId => $details) {
                $product = Product::find($productId);

                if (!$product || $product->stock < $details['quantity']) {
                    throw new \Exception('Product not available in requested quantity.');
                }

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $details['quantity'],
                    'price' => $product->price,
                ]);

                // Update product stock
                $product->stock -= $details['quantity'];
                $product->save();
            }

            // Create customer record if not logged in
            if (!Auth::check()) {
                // You could create a guest customer record here
                // Or require login before checkout
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