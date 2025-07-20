<?php

namespace App\Http\Controllers;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Events\OrderStatusChanged;


class OrderStatusController extends Controller
{

    /**
     * Update order status and manage inventory
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        $order->status = $newStatus;

        if ($newStatus === 'delivered') {
            $order->delivered_at = now();
        }

        $order->save();

        // Dispatch event to handle inventory
        event(new OrderStatusChanged($order, $oldStatus, $newStatus));

        return redirect()->back()->with('success', "Order status updated to {$newStatus}");
    }
}