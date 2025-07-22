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
        $user = Auth::user();

        if ($user->role === 'admin' || $user->role === 'retailer') {
            $orders = Order::with(['items.product', 'user'])->latest()->get();
        } elseif ($user->role === 'supplier') {
            $orders = Order::whereHas('items.product', function ($q) use ($user) {
                $q->where('supplier_id', $user->id);
            })->with(['items.product', 'user'])->latest()->get();
        } else {
            $orders = Order::with(['items.product', 'user'])
                ->where('user_id', $user->id)
                ->latest()->get();
        }

        return view('exports.orders', [
            'orders' => $orders
        ]);
    }
}
