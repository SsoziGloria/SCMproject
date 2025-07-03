<?php
namespace App\Exports;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Auth;

class OrderExport implements FromView
{
    public function view(): View
    {
        // Role-based export logic
        $user = Auth::user();

        if ($user->role === 'admin' || $user->role === 'retailer') {
            $orders = Order::with(['items.product', 'user'])->latest()->get();
        } elseif ($user->role === 'supplier') {
            // Orders containing products supplied by this user
            $orders = Order::whereHas('items.product', function ($q) use ($user) {
                $q->where('supplier_id', $user->id);
            })->with(['items.product', 'user'])->latest()->get();
        } else {
            // Customer: only their own orders
            $orders = Order::with(['items.product', 'user'])
                ->where('user_id', $user->id)
                ->latest()->get();
        }

        return view('exports.orders', [
            'orders' => $orders
        ]);
    }
}