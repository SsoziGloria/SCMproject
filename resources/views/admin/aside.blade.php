@php
    $userManagementRoutes = [
        'users',
        'admin.users.byRole',
        'admin.users.index',
    ];

    $isUserManagementActive = false;
    foreach ($userManagementRoutes as $route) {
        if (
            request()->routeIs($route) || (request()->routeIs('admin.users.byRole') && in_array(
                request()->route('role'),
                ['user', 'retailer', 'supplier', 'admin']
            ))
        ) {
            $isUserManagementActive = true;
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
    ];

    $productReviewRoutes = [
        'productReviews.index',
        'productReviews.create',
        'productReviews.edit',
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

    $vendorManagementRoutes = [
        'admin.vendor-validation',
        'admin.vendor-validation.history',
    ];

    $analyticsRoutes = [
        'analytics',
    ];

    $mlRoutes = [
        'admin.segments',
        'admin.predictions',
    ];

    $inventoryRoutes = [
        'inventories.index',
        'inventories.create',
        'inventories.edit',
        'inventories.adjustments',
        'shipments.index',
    ];

    $settingsRoutes = [
        'admin.settings.index',
    ];

    $isAnalyticsActive = false;
    foreach ($analyticsRoutes as $route) {
        if (request()->routeIs($route)) {
            $isAnalyticsActive = true;
            break;
        }
    }

    $isMLActive = false;
    foreach ($mlRoutes as $route) {
        if (request()->routeIs($route)) {
            $isMLActive = true;
            break;
        }
    }

    $isInventoryActive = false;
    foreach ($inventoryRoutes as $route) {
        if (request()->routeIs($route)) {
            $isInventoryActive = true;
            break;
        }
    }

    $isSettingsActive = false;
    foreach ($settingsRoutes as $route) {
        if (request()->routeIs($route)) {
            $isSettingsActive = true;
            break;
        }
    }

    $workerManagementRoutes = [
        'workers.index',
        'workers.create',
        'workers.edit',
        'workforce.index',
        'workforce.create',
        'workforce.history',
        'workforce.unassigned',
        'tasks.index',
        'tasks.create',
        'tasks.edit',
    ];
    $isworkerManagementActive = false;
    foreach ($workerManagementRoutes as $route) {
        if (request()->routeIs($route)) {
            $isworkerManagementActive = true;
            break;
        }
    }

@endphp

<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? '' : 'collapsed' }}"
                href="{{ route('dashboard') }}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li><!-- End Dashboard Nav -->

        <li class="nav-item">
            <a class="nav-link {{ $isproductManagementActive ? '' : 'collapsed' }}" data-bs-target="#forms-nav"
                data-bs-toggle="collapse" href="#">
                <i class="ri-shopping-bag-2-line"></i><span>Product Management</span><i
                    class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="forms-nav" class="nav-content collapse {{ $isproductManagementActive ? 'show' : '' }}"
                data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('products.index') }}"
                        class="nav-link {{ request()->routeIs('products.index') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>All Products</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('categories.index') }}"
                        class="nav-link {{ request()->routeIs('categories.index') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Categories</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('productReviews.index') }}"
                        class="nav-link {{ request()->routeIs($productReviewRoutes) ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Product Reviews</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-circle"></i><span>Stock Levels</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Product Management Nav -->

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

        <!-- Inventory Management -->
        <li class="nav-item">
            <a class="nav-link {{ $isInventoryActive ? '' : 'collapsed' }}" data-bs-target="#inventory-nav"
                data-bs-toggle="collapse" href="#">
                <i class="bi bi-boxes"></i><span>Inventory Management</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="inventory-nav" class="nav-content collapse {{ $isInventoryActive ? 'show' : '' }}"
                data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('inventories.index') }}"
                        class="nav-link {{ request()->routeIs('inventories.index') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>All Inventory</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('inventories.adjustments') }}"
                        class="nav-link {{ request()->routeIs('inventories.adjustments') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Adjustments</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('shipments.index') }}"
                        class="nav-link {{ request()->routeIs('shipments.index') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Shipments</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Inventory Management Nav -->

        <!--Workforce Management-->
        <li class="nav-item">
            <a class="nav-link {{ $isworkerManagementActive ? '' : 'collapsed' }}" data-bs-target="#icons-nav" data-bs-toggle="collapse" href="#">
                <i class="ri-truck-line"></i><span>Workforce Management</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="icons-nav" class="nav-content collapse {{ $isworkerManagementActive ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('workers.index') }}" class="nav-link {{ request()->routeIs('workers.index') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Workers</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('workforce.index') }}" class="nav-link {{ request()->routeIs('workforce.index') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Assignments</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('tasks.index') }}" class="nav-link {{ request()->routeIs('tasks.index') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Task Management</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('workforce.history') }}" class="nav-link {{ request()->routeIs('workforce.history') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Task History</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('workforce.unassigned') }}" class="nav-link {{ request()->routeIs('workforce.unassigned') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Assigned Workers</span>
                    </a>
                </li>



                {{-- <li>
                    <a href="#">
                        <i class="bi bi-circle"></i><span>Boxicons</span>
                    </a>
                </li> --}}
            </ul>
        </li><!-- End Icons Nav -->

        <li class="nav-heading">Stats</li>

        <!-- ML Analytics -->
        <li class="nav-item">
            <a class="nav-link {{ $isMLActive ? '' : 'collapsed' }}" data-bs-target="#ml-nav"
                data-bs-toggle="collapse" href="#">
                <i class="bi bi-robot"></i><span>ML Analytics</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="ml-nav" class="nav-content collapse {{ $isMLActive ? 'show' : '' }}"
                data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('admin.segments') }}"
                        class="nav-link {{ request()->routeIs('admin.segments') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Customer Segments</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.predictions') }}"
                        class="nav-link {{ request()->routeIs('admin.predictions') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Demand Predictions</span>
                    </a>
                </li>
            </ul>
        </li><!-- End ML Analytics Nav -->

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('analytics') ? '' : 'collapsed' }}" data-bs-target="#charts-nav"
                data-bs-toggle="collapse" href="#">
                <i class="ri-donut-chart-line"></i><span>Analytics</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="charts-nav" class="nav-content collapse {{ $isAnalyticsActive ? 'show' : '' }}"
                data-bs-parent="#sidebar-nav">
                <li>
                    <a class="nav-link {{ request()->routeIs('analytics') ? 'active' : 'collapsed' }}"
                        href=" {{ route('analytics') }}">
                        <i class=" bi bi-circle"></i><span>Dashboard</span>
                    </a>
                </li>
                {{-- <li>
                    <a href="#">
                        <i class="bi bi-circle"></i><span>User Activity Logs</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-circle"></i><span>Product Performance</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-circle"></i><span>Rankings</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-circle"></i><span>System Usage Logs</span>
                    </a>
                </li> --}}
            </ul>
        </li><!-- End Analytics Nav -->

        <li class="nav-item">
            <a class="nav-link {{ request()->fullUrlIs(route('chat.index', '')) ? '' : 'collapsed' }}"
                href="{{ route('chat.index') }}">
                <i class="bi bi-chat-left-quote"></i>
                <span>Chat</span>
            </a>
        </li><!-- End Chat Page Nav -->

        <li class="nav-heading">Account</li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs($userManagementRoutes) ? '' : 'collapsed' }}"
                data-bs-target="#components-nav" data-bs-toggle="collapse" href="#"
                class="{{ request()->fullUrlIs(route('admin.users.byRole', 'user')) ? 'active' : 'collapsed' }}">
                <i class="ri-user-3-line"></i><span>User Management</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components-nav" class="nav-content collapse {{ $isUserManagementActive ? 'show' : '' }} "
                data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('admin.users.index') }}"
                        class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>All Users</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users.byRole', 'user') }}" class="nav-link
                        {{ request()->fullUrlIs(route('admin.users.byRole', 'user')) ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Customers</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users.byRole', 'retailer') }}" class="nav-link
                        {{ request()->fullUrlIs(route('admin.users.byRole', 'retailer')) ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Retailers</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users.byRole', 'supplier') }}" class="nav-link
                        {{ request()->fullUrlIs(route('admin.users.byRole', 'supplier')) ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Suppliers</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users.byRole', 'admin') }}"
                        class="nav-link {{ request()->fullUrlIs(route('admin.users.byRole', 'admin')) ? 'active' : 'collapsed' }}">
                        <i class="bi bi-circle"></i><span>Admins</span>
                    </a>
                </li>
            </ul>
        </li><!-- End User Management Nav -->

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs($vendorManagementRoutes) ? '' : 'collapsed' }}"
                href="{{ route('admin.vendor-validation') }}">
                <i class="bi bi-shield-check"></i>
                <span>Vendor Validation</span>
            </a>
        </li><!-- End Vendor Validation Nav -->

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('profile.show') ? '' : 'collapsed' }}"
                href="{{ route('profile.show') }}">
                <i class="bi bi-person"></i>
                <span>Profile</span>
            </a>
        </li><!-- End Profile Page Nav -->

        <!-- Settings -->
        <li class="nav-item">
            <a class="nav-link {{ $isSettingsActive ? '' : 'collapsed' }}"
                href="{{ route('admin.settings.index') }}">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
            </a>
        </li><!-- End Settings Nav -->

    </ul>

</aside>