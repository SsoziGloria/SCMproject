@extends('admin.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h3 mb-0 text-gray-800">User Details</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $user->name }}</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary me-2">
                    <i class="bi bi-pencil"></i> Edit User
                </a>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                    data-bs-target="#deleteUserModal">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <!-- User Profile Card -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center">
                        <div class="mt-3 mb-4">
                            @if($user->profile_photo)
                                <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}"
                                    class="rounded-circle img-fluid"
                                    style="width: 150px; height: 150px; object-fit: cover; align-items: center;">
                            @else
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto"
                                    style="width: 150px; height: 150px; font-size: 3rem;">
                                    <i class="bi bi-person text-secondary"></i>
                                </div>
                            @endif
                        </div>

                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <p class="text-muted mb-3">{{ $user->email }}</p>

                        <div class="mb-3">
                            <span class="badge rounded-pill bg-{{ $user->role == 'admin' ? 'danger' :
        ($user->role == 'retailer' ? 'success' :
            ($user->role == 'supplier' ? 'warning' : 'primary')) }} px-3 py-2">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-center mb-2">
                            <button type="button" class="btn btn-outline-primary me-2" data-bs-toggle="modal"
                                data-bs-target="#resetPasswordModal">
                                <i class="bi bi-key"></i> Reset Password
                            </button>
                        </div>
                    </div>
                </div>

                <!-- User Stats -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold">User Statistics</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Registration Date
                                <span>{{ $user->created_at->format('M d, Y') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Last Login
                                <span>{{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Orders
                                <span class="badge bg-primary rounded-pill">{{ $user->orders_count ?? 0 }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Total Spent
                                <span>UGX {{ number_format($user->total_spent ?? 0, 0) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Account Status
                                <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }} rounded-pill">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- User Details Tabs -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="userTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="details-tab" data-bs-toggle="tab"
                                    data-bs-target="#details" type="button" role="tab" aria-controls="details"
                                    aria-selected="true">Details</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders"
                                    type="button" role="tab" aria-controls="orders" aria-selected="false">Orders</button>
                            </li>
                            @if($user->role == 'supplier')
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#products"
                                        type="button" role="tab" aria-controls="products"
                                        aria-selected="false">Products</button>
                                </li>
                            @endif
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="activities-tab" data-bs-toggle="tab"
                                    data-bs-target="#activities" type="button" role="tab" aria-controls="activities"
                                    aria-selected="false">Activity Log</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="userTabsContent">
                            <!-- User Details Tab -->
                            <div class="tab-pane fade show active" id="details" role="tabpanel"
                                aria-labelledby="details-tab">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3">Personal Information</h6>
                                        <table class="table">
                                            <tr>
                                                <th style="width: 30%">Full Name</th>
                                                <td>{{ $user->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Email</th>
                                                <td>{{ $user->email }}</td>
                                            </tr>
                                            <tr>
                                                <th>Phone</th>
                                                <td>{{ $user->phone ?? 'Not provided' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Gender</th>
                                                <td>{{ ucfirst($user->gender ?? 'Not specified') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3">Address Information</h6>
                                        <table class="table">
                                            <tr>
                                                <th style="width: 30%">Address</th>
                                                <td>{{ $user->address ?? 'Not provided' }}</td>
                                            </tr>
                                            <tr>
                                                <th>City</th>
                                                <td>{{ $user->city ?? 'Not provided' }}</td>
                                            </tr>
                                            <tr>
                                                <th>State</th>
                                                <td>{{ $user->state ?? 'Not provided' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Country</th>
                                                <td>{{ $user->country ?? 'Not provided' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                @if($user->role == 'supplier')
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="fw-bold mb-3">Business Information</h6>
                                            <table class="table">
                                                <tr>
                                                    <th style="width: 30%">Company Name</th>
                                                    <td>{{ $user->company_name ?? 'Not provided' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Business Type</th>
                                                    <td>{{ $user->business_type ?? 'Not provided' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Tax ID</th>
                                                    <td>{{ $user->tax_id ?? 'Not provided' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Description</th>
                                                    <td>{{ $user->description ?? 'Not provided' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- User Orders Tab -->
                            <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Order #</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Total</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($orders ?? [] as $order)
                                                                                <tr>
                                                                                    <td>{{ $order->order_number }}</td>
                                                                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                                                                    <td>
                                                                                        <span
                                                                                            class="badge bg-{{ 
                                                                                                                                                                                $order->status == 'pending' ? 'warning' :
                                                ($order->status == 'processing' ? 'info' :
                                                    ($order->status == 'shipped' ? 'primary' :
                                                        ($order->status == 'delivered' ? 'success' : 'danger'))) 
                                                                                                                                                                            }}">
                                                                                            {{ ucfirst($order->status) }}
                                                                                        </span>
                                                                                    </td>
                                                                                    <td>UGX {{ number_format($order->total_amount, 0) }}</td>
                                                                                    <td>
                                                                                        <a href="{{ route('orders.show', $order->id) }}"
                                                                                            class="btn btn-sm btn-outline-primary">
                                                                                            <i class="bi bi-eye"></i>
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-4">No orders found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                @if(isset($orders) && $orders->hasPages())
                                    <div class="d-flex justify-content-center mt-4">
                                        {{ $orders->links() }}
                                    </div>
                                @endif
                            </div>

                            <!-- User Products Tab (for suppliers) -->
                            @if($user->role == 'supplier')
                                <div class="tab-pane fade" id="products" role="tabpanel" aria-labelledby="products-tab">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Category</th>
                                                    <th>Price</th>
                                                    <th>Stock</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($products ?? [] as $product)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                @if($product->image)
                                                                    <img src="{{ asset('storage/' . $product->image) }}"
                                                                        alt="{{ $product->name }}" class="rounded me-3"
                                                                        style="width: 50px; height: 50px; object-fit: cover;">
                                                                @else
                                                                    <div class="bg-light rounded me-3"
                                                                        style="width: 50px; height: 50px;"></div>
                                                                @endif
                                                                <div>
                                                                    <div class="fw-bold">{{ $product->name }}</div>
                                                                    <div class="small text-muted">SKU: {{ $product->sku }}</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                                                        <td>UGX {{ number_format($product->price, 0) }}</td>
                                                        <td>{{ $product->stock ?? 0 }}</td>
                                                        <td>
                                                            <a href="{{ route('admin.products.show', $product->id) }}"
                                                                class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-4">No products found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    @if(isset($products) && $products->hasPages())
                                        <div class="d-flex justify-content-center mt-4">
                                            {{ $products->links() }}
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- User Activity Tab -->
                            <div class="tab-pane fade" id="activities" role="tabpanel" aria-labelledby="activities-tab">
                                <ul class="timeline">
                                    @forelse($activities ?? [] as $activity)
                                                                <li class="timeline-item mb-4">
                                                                    <div class="timeline-marker bg-{{ 
                                                                                                                                            $activity->type == 'login' ? 'success' :
                                        ($activity->type == 'order' ? 'primary' :
                                            ($activity->type == 'profile_update' ? 'info' : 'secondary'))
                                                                                                                                        }}"></div>
                                                                    <div class="timeline-content">
                                                                        <h6 class="mb-1">{{ ucfirst($activity->type) }}</h6>
                                                                        <p class="text-muted mb-0">{{ $activity->description }}</p>
                                                                        <small
                                                                            class="text-muted">{{ $activity->created_at->format('M d, Y H:i') }}</small>
                                                                    </div>
                                                                </li>
                                    @empty
                                        <li class="text-center py-5">
                                            <p class="text-muted">No activity recorded</p>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete User Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel">Delete User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete {{ $user->name }}? This action cannot be undone.</p>
                    <p class="text-danger">All associated data may be permanently deleted.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resetPasswordModalLabel">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="new_password_confirmation"
                                name="new_password_confirmation" required>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="send_notification" name="send_notification"
                                checked>
                            <label class="form-check-label" for="send_notification">
                                Send password reset notification to user
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Timeline Styling */
        .timeline {
            position: relative;
            list-style: none;
            padding-left: 0;
        }

        .timeline-item {
            position: relative;
            padding-left: 40px;
        }

        .timeline-marker {
            position: absolute;
            left: 0;
            top: 0;
            width: 15px;
            height: 15px;
            border-radius: 50%;
        }

        .timeline:before {
            content: '';
            position: absolute;
            left: 7px;
            top: 0;
            height: 100%;
            width: 2px;
            background-color: #e9ecef;
        }
    </style>
@endsection