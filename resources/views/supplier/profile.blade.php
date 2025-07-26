@extends('profile.layout')

@section('title', 'content')

@section('content')
    @php
$productManagementRoutes = [
    'products.index',
    'supplier.products.index',
    'supplier.products.create',
    'supplier.products.show',
    'supplier.products.edit',
    'categories.index',
    'categories.create',
    'categories.edit',
    'productReviews.index',
    'productReviews.create',
    'productReviews.edit',
    'inventories.create',
];

$isproductManagementActive = false;
foreach ($productManagementRoutes as $route) {
    if (request()->routeIs($route)) {
        $isproductManagementActive = true;
        break;
    }
}

$orderManagementRoutes = [
    'orders.incoming',
    'orders.details',
    'orders.reject',
    'orders.index',
    'orders.pending',
    'orders',
    'orders.show',
    'orders.edit',
];

$isorderManagementActive = false;
foreach ($orderManagementRoutes as $route) {
    if (request()->routeIs($route)) {
        $isorderManagementActive = true;
        break;
    }
}

$supplierManagementRoutes = [
    'supplier',
    'supplier.approved',
    'supplier.requests',
    'supplier.orders',
    'supplier.messages',
];

$isSupplierManagementActive = false;
foreach ($supplierManagementRoutes as $route) {
    if (request()->routeIs($route)) {
        $isSupplierManagementActive = true;
        break;
    }
}

$inventoryRoutes = [
    'inventories.index',
    'inventories.create',
    'inventories.edit',
    'inventories.show',
    'inventories.reorders',
    'inventories.adjustments',
    'inventories.adjustments.create',
    'inventories.export',
    'inventories.history',
];

$isInventoryActive = false;
foreach ($inventoryRoutes as $route) {
    if (request()->routeIs($route)) {
        $isInventoryActive = true;
        break;
    }
}

$vendorRoutes = [
    'vendor.verification.form',
    'vendor.verification.pending',
    'vendor.verification.approved',
];

$isVendorActive = false;
foreach ($vendorRoutes as $route) {
    if (request()->routeIs($route)) {
        $isVendorActive = true;
        break;
    }
}

@endphp

<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link {{ request()->fullUrlIs(route('dashboard', '')) ? '' : 'collapsed' }}"
                href="{{ route('dashboard') }}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li><!-- End Dashboard Nav -->

        <li class="nav-item">
            <a class="nav-link {{ $isproductManagementActive ? '' : 'collapsed' }}" data-bs-target="#products-nav"
                data-bs-toggle="collapse" href="#">
                <i class="ri-shopping-bag-2-line"></i><span>Product Management</span><i
                    class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="products-nav" class="nav-content collapse {{ $isproductManagementActive ? 'show' : '' }}"
                data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('supplier.products.index') }}"
                        class="nav-link {{ request()->routeIs('supplier.products.index') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>My Products</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('supplier.products.create') }}"
                        class="nav-link {{ request()->routeIs('supplier.products.create') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Add New Product</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('inventories.create') }}"
                        class="nav-link {{ request()->routeIs('inventories.create') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Add Inventory</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Product Management Nav -->

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs($orderManagementRoutes) ? '' : 'collapsed' }}"
                data-bs-target="#orders-nav" data-bs-toggle="collapse" href="#">
                <i class="ri-swap-box-line"></i><span>Order Management</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="orders-nav" class="nav-content collapse {{ $isorderManagementActive ? 'show' : '' }}"
                data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('orders.index') }}"
                        class="nav-link {{ request()->fullUrlIs(route('orders.index', '')) ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>All Orders</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('orders.index', ['status' => 'pending']) }}"
                        class="nav-link {{ request()->fullUrlIs(route('orders.index', ['status' => 'pending'])) ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Pending</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('orders.index', ['status' => 'shipped']) }}"
                        class="nav-link {{ request()->fullUrlIs(route('orders.index', ['status' => 'shipped'])) ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Shipped</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('orders.index', ['status' => 'delivered']) }}"
                        class="nav-link {{ request()->fullUrlIs(route('orders.index', ['status' => 'delivered'])) ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Delivered</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('orders.index', ['status' => 'cancelled']) }}"
                        class="nav-link {{ request()->fullUrlIs(route('orders.index', ['status' => 'cancelled'])) ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Cancelled</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Order Management Nav -->

        <!-- Vendor Verification Nav -->
        <li class="nav-item">
            <a class="nav-link {{ $isVendorActive ? '' : 'collapsed' }}" data-bs-target="#vendor-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-shield-check"></i><span>Vendor Verification</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="vendor-nav" class="nav-content collapse {{ $isVendorActive ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('vendor.verification.form') }}" class="nav-link {{ request()->routeIs('vendor.verification.form') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Submit Verification</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Vendor Verification Nav -->

        <li class="nav-heading">Communication</li>

        <li class="nav-item">
            <a class="nav-link {{ request()->fullUrlIs(route('chat.index', '')) ? '' : 'collapsed' }}"
                href="{{ route('chat.index') }}">
                <i class="bi bi-chat-left-quote"></i>
                <span>Chat</span>
            </a>
        </li><!-- End Chat Page Nav -->

        <li class="nav-heading">Account</li>

        <li class="nav-item">
            <a class="nav-link {{ request()->fullUrlIs(route('profile.show', '')) ? '' : 'collapsed' }}"
                href="{{ route('profile.show') }}">
                <i class="bi bi-person"></i>
                <span>Profile</span>
            </a>
        </li><!-- End Profile Page Nav -->

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('faq') ? '' : 'collapsed' }}" href="{{ route('faq') }}">
                <i class="bi bi-question-circle"></i>
                <span>F.A.Q</span>
            </a>
        </li><!-- End F.A.Q Page Nav -->

    </ul>

</aside>
@endsection