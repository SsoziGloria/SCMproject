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
        'products',
        'categories.index',
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

    $inventoryRoutes = [
        'inventories.index',
        'inventories.reorders',
        'inventories.adjustments',
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
                data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
                <i class="ri-swap-box-line"></i><span>Order Management</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="tables-nav" class="nav-content collapse {{ $isorderManagementActive ? 'show' : '' }}"
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

        <!-- Product Management Nav -->
        <li class="nav-item">
            <a class="nav-link {{ $isProductManagementActive ? '' : 'collapsed' }}" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
                <i class="ri-shopping-bag-2-line"></i><span>Product Catalog</span><i
                    class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="forms-nav" class="nav-content collapse {{ $isProductManagementActive ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.index') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>All Products</span>
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
                <li>
                    <a href="{{ route('stockLevels.index') }}" class="nav-link {{ request()->routeIs('stockLevels.index') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Stock Levels</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Product Management Nav -->

        <li class="nav-item">
            <a class="nav-link {{ $isInventoryActive ? '' : 'collapsed' }}" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
                <i class="ri-clipboard-line"></i><span>Inventory</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="tables-nav" class="nav-content collapse {{ $isInventoryActive ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('inventories.index') }}" class="nav-link {{ request()->routeIs('inventories.index') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Current Stock</span>
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
        </li><!-- End Inventory Nav -->

        <!-- Analytics & Reports Nav -->
        <li class="nav-item">
            <a class="nav-link {{ $isAnalyticsActive ? '' : 'collapsed' }}" data-bs-target="#analytics-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-bar-chart"></i><span>Analytics & Reports</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="analytics-nav" class="nav-content collapse {{ $isAnalyticsActive ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('retailer.analytics.index') }}" class="nav-link {{ request()->routeIs('retailer.analytics.index') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Analytics Overview</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('retailer.analytics.sales') }}" class="nav-link {{ request()->routeIs('retailer.analytics.sales') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Sales Analytics</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('retailer.analytics.inventory') }}" class="nav-link {{ request()->routeIs('retailer.analytics.inventory') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Inventory Analytics</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('retailer.analytics.customers') }}" class="nav-link {{ request()->routeIs('retailer.analytics.customers') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Customer Analytics</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('retailer.reports.index') }}" class="nav-link {{ request()->routeIs('retailer.reports.index') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Generate Reports</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Analytics & Reports Nav -->

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