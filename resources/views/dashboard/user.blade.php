{{-- filepath: /Users/user/chocolate-scm/resources/views/dashboard/user.blade.php --}}
@extends('user.app')

@section('content')
    <div class="pagetitle">
        <h1>Welcome Back!</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.user') }}">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div>

    <section class="section dashboard">
        <div class="row">
            <!-- Welcome Message -->
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h4 class="mb-1">Good {{ now()->format('A') === 'AM' ? 'morning' : 'afternoon' }},
                                    {{ Auth::user()->name }}!</h4>
                                <p class="text-muted mb-0">Here's what's happening with your chocolate orders today.</p>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="{{ route('shop.index') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i> New Order
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="col-md-4">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">My Orders</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-cart-check text-primary"></i>
                            </div>
                            <div class="ps-3">
                                <h6 class="text-primary">{{ Auth::user()->orders->count() }}</h6>
                                <span class="text-muted small">Total orders</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">In Transit</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-truck text-warning"></i>
                            </div>
                            <div class="ps-3">
                                <h6 class="text-warning">{{ Auth::user()->orders->where('status', 'shipped')->count() }}
                                </h6>
                                <span class="text-muted small">Shipments on the way</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">Delivered</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-check-circle text-success"></i>
                            </div>
                            <div class="ps-3">
                                <h6 class="text-success">{{ Auth::user()->orders->where('status', 'delivered')->count() }}
                                </h6>
                                <span class="text-muted small">This month</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-lg-8">
                <!-- My Recent Orders -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">My Recent Orders</h5>
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-primary btn-sm">
                                View All Orders
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Product</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(Auth::user()->orders->sortByDesc('created_at')->take(3) as $order)
                                        <tr>
                                            <td><strong>{{ $order->order_number }}</strong></td>
                                            <td>
                                                @if($order->items->isNotEmpty())
                                                    <div class="d-flex align-items-center">
                                                        @php $product = $order->items->first()->product @endphp
                                                        @if($product && $product->image)
                                                            <img src="{{ asset('storage/' . $product->image) }}"
                                                                alt="{{ $product->name }}" class="rounded me-2" width="32" height="32">
                                                        @else
                                                            <div class="bg-light rounded me-2" style="width:32px;height:32px;"></div>
                                                        @endif
                                                        <div>
                                                            <div class="fw-bold">{{ $product->name ?? 'Product' }}</div>
                                                            <small class="text-muted">
                                                                @if($order->items->count() > 1)
                                                                    +{{ $order->items->count() - 1 }} more items
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">No items</span>
                                                @endif
                                            </td>
                                            <td>{{ number_format($order->total_amount, 0, '.', ',') }} UGX</td>
                                            <td>
                                                @if($order->status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($order->status == 'processing')
                                                    <span class="badge bg-info">Processing</span>
                                                @elseif($order->status == 'shipped')
                                                    <span class="badge bg-primary">Shipped</span>
                                                @elseif($order->status == 'delivered')
                                                    <span class="badge bg-success">Delivered</span>
                                                @elseif($order->status == 'cancelled')
                                                    <span class="badge bg-danger">Cancelled</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('orders.show', $order->id) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="bi bi-cart-x fs-2"></i>
                                                    <p class="mt-2">No orders yet</p>
                                                    <a href="{{ route('shop.index') }}"
                                                        class="btn btn-sm btn-primary mt-1">Start Shopping</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- <!-- Order Tracking -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Track Your Shipment</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <form action="#" method="GET"> <!--Track: { route('orders.track') }} -->
                                    <div class="mb-3">
                                        <label for="trackingNumber" class="form-label">Enter Order Number</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="trackingNumber" name="order_number"
                                                placeholder="e.g., ORD-2025######">
                                            <button class="btn btn-primary" type="submit">Track</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Need help?</strong> Contact our support team for assistance with tracking your
                                    orders.
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}

                <!-- Support Contact -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Need Help?</h5>
                        <p class="text-muted">Our customer service team is here to help you with any questions about your
                            orders.</p>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <i class="bi bi-telephone text-primary fs-4"></i>
                                    <h6 class="mt-2">Call Us</h6>
                                    <p class="text-muted small">{{ config('app.support_phone', '+256-778-123-456') }}</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <i class="bi bi-envelope text-primary fs-4"></i>
                                <h6 class="mt-2">Email Us</h6>
                                <p class="text-muted small">{{ config('app.support_email', 'support@chocolatechain.com') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Quick Actions</h5>
                        <div class="d-grid gap-2">
                            <a href="{{ route('shop.index') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Place New Order
                            </a>
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">
                                <i class="bi bi-list-ul me-2"></i>View All Orders
                            </a>
                            <a href="{{ route('shop.index') }}" class="btn btn-outline-primary">
                                <i class="bi bi-grid-3x3-gap me-2"></i>Browse Catalog
                            </a>
                            {{-- <a href="#" class="btn btn-outline-secondary">
                                <i class="bi bi-headset me-2"></i>Get Support
                            </a> --}}
                        </div>
                    </div>
                </div>

                <!-- Recent Updates -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Updates</h5>
                        <div class="activity">
                            @php
                                $updates = Auth::user()->orders()
                                    ->orderBy('updated_at', 'desc')
                                    ->take(4)
                                    ->get()
                                    ->map(function ($order) {
                                        $timePassed = $order->updated_at->diffForHumans();
                                        $message = '';
                                        $badgeClass = '';

                                        if ($order->status == 'delivered') {
                                            $message = "Your order <strong>{$order->order_number}</strong> has been delivered!";
                                            $badgeClass = 'text-success';
                                        } elseif ($order->status == 'shipped') {
                                            $message = "Your order <strong>{$order->order_number}</strong> has been shipped and is on its way!";
                                            $badgeClass = 'text-primary';
                                        } elseif ($order->status == 'processing') {
                                            $message = "Order <strong>{$order->order_number}</strong> is being processed at our facility";
                                            $badgeClass = 'text-info';
                                        } elseif ($order->status == 'pending') {
                                            $message = "Thank you for placing order <strong>{$order->order_number}</strong>";
                                            $badgeClass = 'text-warning';
                                        }

                                        return [
                                            'time' => $timePassed,
                                            'message' => $message,
                                            'badgeClass' => $badgeClass
                                        ];
                                    });
                            @endphp

                            @forelse($updates as $update)
                                <div class="activity-item d-flex">
                                    <div class="activite-label">{{ $update['time'] }}</div>
                                    <i
                                        class='bi bi-circle-fill activity-badge {{ $update['badgeClass'] }} align-self-start'></i>
                                    <div class="activity-content">
                                        {!! $update['message'] !!}
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-3">
                                    <p class="text-muted">No recent updates</p>
                                </div>
                            @endforelse

                            @if($updates->isEmpty())
                                <div class="activity-item d-flex">
                                    <div class="activite-label">Now</div>
                                    <i class='bi bi-circle-fill activity-badge text-info align-self-start'></i>
                                    <div class="activity-content">
                                        Welcome to your Chocolate Supply Chain dashboard!
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection