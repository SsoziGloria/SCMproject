@php
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

    $productManagementRoutes = [
        'products.index',
        'products.create',
        'products.show',
        'products.edit',
        'products',
        'categories.index',
        'categories.create',
        'categories.show',
        'categories.edit',
        'productReviews.index',
        'productReviews.create',
        'productReviews.edit',
        'stockLevels.index',
    ];

    $isProductManagementActive = false;
    foreach ($productManagementRoutes as $route) {
        if (request()->routeIs($route)) {
            $isProductManagementActive = true;
            break;
        }
    }

    $shipmentRoutes = [
        'shipments.index',
        'shipments.create',
        'shipments.edit',
        'shipments.show',
    ];

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

    $analyticsRoutes = [
        'retailer.analytics.index',
        'retailer.analytics.sales',
        'retailer.analytics.inventory', 
        'retailer.analytics.customers',
        'retailer.reports.index',
        'retailer.reports.generate',
        'retailer.reports.download',
        'analytics',
        'sales',
    ];

    $isAnalyticsActive = false;
    foreach ($analyticsRoutes as $route) {
        if (request()->routeIs($route)) {
            $isAnalyticsActive = true;
            break;
        }
    }

    $supplierRoutes = [
        'supplier.approved',
        'supplier.requests',
    ];

    $isSupplierActive = false;
    foreach ($supplierRoutes as $route) {
        if (request()->routeIs($route)) {
            $isSupplierActive = true;
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
            <a class="nav-link {{ request()->routeIs('retailer.dashboard') ? '' : 'collapsed' }}"
                href="{{ route('retailer.dashboard') }}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li><!-- End Dashboard Nav -->

        <!-- Begin Order Management Nav -->
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

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('shipments.index') ? '' : 'collapsed' }}"
                href="{{ route('shipments.index') }}" class="nav-link {{ request()->routeIs($shipmentRoutes) ? 'active' : 'collapsed' }}">
                <i class="bi bi-truck"></i>
                <span>Shipments</span>
            </a>
        </li><!-- End Shipment Management Nav -->

        <!-- Product Management Nav -->
        <li class="nav-item">
            <a class="nav-link {{ $isProductManagementActive ? '' : 'collapsed' }}" data-bs-target="#products-nav" data-bs-toggle="collapse" href="#">
                <i class="ri-shopping-bag-2-line"></i><span>Product Catalog</span><i
                    class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="products-nav" class="nav-content collapse {{ $isProductManagementActive ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.index') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>All Products</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('products.create') }}" class="nav-link {{ request()->routeIs('products.create') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Add Product</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.index') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Categories</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('productReviews.index') }}" class="nav-link {{ request()->routeIs('productReviews.index') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Product Reviews</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Product Management Nav -->

        <li class="nav-item">
            <a class="nav-link {{ $isInventoryActive ? '' : 'collapsed' }}" data-bs-target="#inventory-nav" data-bs-toggle="collapse" href="#">
                <i class="ri-clipboard-line"></i><span>Inventory Management</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="inventory-nav" class="nav-content collapse {{ $isInventoryActive ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('inventories.index') }}" class="nav-link {{ request()->routeIs('inventories.index') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Current Stock</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('inventories.create') }}" class="nav-link {{ request()->routeIs('inventories.create') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Add New Item</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('inventories.reorders') }}" class="nav-link {{ request()->routeIs('inventories.reorders') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Reorders</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('inventories.adjustments') }}" class="nav-link {{ request()->routeIs('inventories.adjustments') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Adjustments</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ $isSupplierActive ? '' : 'collapsed' }}" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
                <i class="ri-account-box-line"></i><span>mySupplier</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="charts-nav" class="nav-content collapse {{ $isSupplierActive ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('supplier.approved') }}" class="nav-link {{ request()->routeIs('supplier.approved') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Approved</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('supplier.requests') }}" class="nav-link {{ request()->routeIs('supplier.requests') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>New Requests</span>
                    </a>
                </li>
            </ul>
        </li><!-- End mySupplier Nav -->

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
            <a class="nav-link {{ request()->routeIs('chat.index') ? '' : 'collapsed' }}"
                href="{{ route('chat.index') }}">
                <i class="bi bi-chat-left-quote"></i>
                <span>Chat</span>
            </a>
        </li><!-- End Chat Page Nav -->

        <li class="nav-heading">Account</li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('profile.show') ? '' : 'collapsed' }}"
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