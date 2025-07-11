<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Display the shopping cart
     */
    public function index()
    {
        $cart = Session::get('cart', []);
        $products = [];
        $total = 0;

        // Get product details for each cart item
        if (!empty($cart)) {
            foreach ($cart as $id => $details) {
                $product = Product::find($id);
                if ($product) {
                    $products[$id] = [
                        'product' => $product,
                        'quantity' => $details['quantity']
                    ];
                    $total += $product->price * $details['quantity'];
                }
            }
        }

        return view('cart.index', compact('products', 'total'));
    }

    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $productId = $validated['product_id'];
        $quantity = $validated['quantity'];

        // Check product exists and has enough stock
        $product = Product::find($productId);
        if (!$product) {
            return back()->with('error', 'Product not found.');
        }

        if ($product->stock < $quantity) {
            return back()->with('error', 'Not enough stock available.');
        }

        // Get current cart
        $cart = Session::get('cart', []);

        // Check if product already in cart
        if (isset($cart[$productId])) {
            // Update quantity if already in cart
            $newQuantity = $cart[$productId]['quantity'] + $quantity;

            // Check if new quantity exceeds stock
            if ($newQuantity > $product->stock) {
                return back()->with('error', 'Cannot add more of this item (exceeds available stock).');
            }

            $cart[$productId]['quantity'] = $newQuantity;
        } else {
            // Add to cart if not already there
            $cart[$productId] = [
                'quantity' => $quantity
            ];
        }

        // Update cart in session
        Session::put('cart', $cart);

        return back()->with('success', 'Product added to cart!');
    }

    /**
     * Update cart quantities
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1'
        ]);

        $cart = Session::get('cart', []);
        $quantities = $validated['quantities'];

        foreach ($quantities as $productId => $quantity) {
            // Validate product exists and has enough stock
            $product = Product::find($productId);

            if ($product && $product->stock >= $quantity) {
                if (isset($cart[$productId])) {
                    $cart[$productId]['quantity'] = $quantity;
                }
            } else {
                return back()->with('error', 'One or more products have insufficient stock.');
            }
        }

        Session::put('cart', $cart);
        return back()->with('success', 'Cart updated successfully.');
    }

    /**
     * Remove item from cart
     */
    public function remove($id)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);
        }

        return back()->with('success', 'Product removed from cart.');
    }

    /**
     * Clear the entire cart
     */
    public function clear()
    {
        Session::forget('cart');
        return back()->with('success', 'Cart cleared.');
    }

    /**
     * Proceed to checkout
     */
    public function checkout()
    {
        // Validate cart has items
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Validate all products in cart still have sufficient stock
        $insufficientStock = false;
        foreach ($cart as $id => $details) {
            $product = Product::find($id);
            if (!$product || $product->stock < $details['quantity']) {
                $insufficientStock = true;
                break;
            }
        }

        if ($insufficientStock) {
            return redirect()->route('cart.index')
                ->with('error', 'Some products in your cart no longer have sufficient stock. Please update your cart.');
        }

        // Proceed to checkout - either render checkout form or redirect to payment gateway
        return view('checkout.index');
    }
}