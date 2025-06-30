<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        $results = [
            'users' => [],
            'products' => [],
            'orders' => [],
        ];

        if (!$query) {
            return view('search.results', compact('results', 'query'));
        }

        // Search users
        $results['users'] = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('role', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        // Search products
        $results['products'] = Product::where('name', 'like', "%{$query}%")
            ->orWhere('product_id', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orWhere('ingredients', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        // Search orders
        $user = Auth::user();
        $orderQuery = Order::where(function ($q) use ($query) {
            $q->where('order_number', 'like', "%{$query}%")
                ->orWhere('status', 'like', "%{$query}%")
                ->orWhere('shipping_address', 'like', "%{$query}%");
        });

        // Limit results based on user role for security
        if ($user->role === 'admin') {
            // Admin can see all orders
        } elseif ($user->role === 'supplier') {
            // Suppliers can only see orders assigned to them
            $orderQuery->where('supplier_id', $user->id);
        } elseif ($user->role === 'retailer') {
            // Retailers see only their orders
            $orderQuery->where('user_id', $user->id);
        } else {
            // Regular users see only their orders
            $orderQuery->where('user_id', $user->id);
        }

        $results['orders'] = $orderQuery->limit(10)->get();

        return view('search.results', compact('results', 'query'));
    }

    /**
     * Advanced search with filters
     */
    public function advanced(Request $request)
    {
        $query = $request->input('query');
        $category = $request->input('category');
        $type = $request->input('type');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        if ($category === 'products') {
            $results = Product::query();

            if ($query) {
                $results->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('product_id', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                });
            }

            if ($type) {
                $results->where('category', $type);
            }

            $results = $results->paginate(15);

            return view('search.advanced', compact('results', 'query', 'category', 'type', 'dateFrom', 'dateTo'));
        }

        // Similar logic for other categories (orders, users, etc.)

        return view('search.advanced');
    }
}