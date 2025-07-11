<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShipmentController extends Controller
{
    /**
     * Display a listing of the shipments.
     */
    public function index(Request $request)
    {
        // Base query
        $query = Shipment::with(['supplier', 'product']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Apply date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort options
        $sort = $request->get('sort', 'newest');

        if ($sort === 'newest') {
            $query->latest();
        } elseif ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'expected_delivery') {
            $query->orderBy('expected_delivery', 'asc');
        }

        // Role-based filtering
        $user = Auth::user();
        if ($user->role === 'supplier') {
            $query->where('supplier_id', $user->id);
        }

        // Get shipments with pagination
        $shipments = $query->paginate(15)->withQueryString();

        // Get all suppliers for filter dropdown (for admin/retailer only)
        $suppliers = [];
        if (in_array($user->role, ['admin', 'retailer'])) {
            $suppliers = User::where('role', 'supplier')->get();
        }

        // Stats for dashboard cards
        $stats = [
            'total' => Shipment::count(),
            'pending' => Shipment::pending()->count(),
            'shipped' => Shipment::shipped()->count(),
            'delivered' => Shipment::delivered()->count(),
            'overdue' => Shipment::overdue()->count(),
        ];

        return view('shipments.index', compact('shipments', 'suppliers', 'stats'));
    }

    /**
     * Show the form for creating a new shipment.
     */
    public function create()
    {
        $products = Product::all();
        $suppliers = User::where('role', 'supplier')->get();
        return view('shipments.create', compact('products', 'suppliers'));
    }

    /**
     * Store a newly created shipment in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'expected_delivery' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string',
        ]);

        $shipment = new Shipment($validated);
        $shipment->shipment_number = Shipment::generateShipmentNumber();
        $shipment->status = 'pending';
        $shipment->save();

        return redirect()->route('shipments.index')
            ->with('success', 'Shipment created successfully.');
    }

    /**
     * Display the specified shipment.
     */
    public function show(Shipment $shipment)
    {
        return view('shipments.show', compact('shipment'));
    }

    /**
     * Show the form for editing the specified shipment.
     */
    public function edit(Shipment $shipment)
    {
        $products = Product::all();
        $suppliers = User::where('role', 'supplier')->get();
        return view('shipments.edit', compact('shipment', 'products', 'suppliers'));
    }

    /**
     * Update the specified shipment in storage.
     */
    public function update(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|in:pending,shipped,delivered,cancelled',
            'expected_delivery' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Handle shipment status changes
        if ($validated['status'] !== $shipment->status) {
            if ($validated['status'] === 'shipped' && is_null($shipment->shipped_at)) {
                $validated['shipped_at'] = now();
            } elseif ($validated['status'] === 'delivered' && is_null($shipment->delivered_at)) {
                $validated['delivered_at'] = now();
            }
        }

        $shipment->update($validated);

        return redirect()->route('shipments.index')
            ->with('success', 'Shipment updated successfully.');
    }

    /**
     * Update the status of a shipment.
     */
    public function updateStatus(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,shipped,delivered,cancelled',
        ]);

        // Check if the status change is allowed
        $allowed = false;
        switch ($validated['status']) {
            case 'shipped':
                $allowed = $shipment->canBeShipped();
                if ($allowed)
                    $shipment->shipped_at = now();
                break;
            case 'delivered':
                $allowed = $shipment->canBeDelivered();
                if ($allowed)
                    $shipment->delivered_at = now();
                break;
            case 'cancelled':
                $allowed = $shipment->canBeCancelled();
                break;
            default:
                $allowed = true;
        }

        if (!$allowed) {
            return back()->with('error', 'Invalid status change.');
        }

        $shipment->status = $validated['status'];
        $shipment->save();

        return redirect()->route('shipments.index')
            ->with('success', 'Shipment status updated successfully.');
    }

    /**
     * Remove the specified shipment from storage.
     */
    public function destroy(Shipment $shipment)
    {
        $shipment->delete();

        return redirect()->route('shipments.index')
            ->with('success', 'Shipment deleted successfully.');
    }
}