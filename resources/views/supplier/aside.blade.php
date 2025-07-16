@php
    // List all route names for User Management group
<<<<<<< Updated upstream
    $productManagementRoutes = [
        'products.index',
        'products',
        'categories.index',
        // add more if needed
    ];
    // Check if current route matches any in the group
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
=======
    $userManagementRoutes = [
        'users',
        'admin.users.byRole',
        // add more if needed
    ];
    // Check if current route matches any in the group
    $isUserManagementActive = false;
    foreach ($userManagementRoutes as $route) {
        if (request()->routeIs($route) || (request()->routeIs('admin.users.byRole') && in_array(request()->route('role'), ['user', 'retailer', 'supplier', 'admin']))) {
            $isUserManagementActive = true;
>>>>>>> Stashed changes
            break;
        }
    }
@endphp

<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link {{ request()->fullUrlIs(route('dashboard.supplier', '')) ? '' : 'collapsed' }}"
                href="{{ route('dashboard') }}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li><!-- End Dashboard Nav -->

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs($productManagementRoutes) ? '' : 'collapsed' }}"
                data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#"
                class="{{ request()->fullUrlIs(route('products.index', '')) ? 'collapsed' : 'collapsed' }}">
                <i class="ri-shopping-bag-2-line"></i><span>Product Management</span><i
                    class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="forms-nav" class="nav-content collapse {{ $isproductManagementActive ? 'show' : '' }}"
                data-bs-parent="#sidebar-nav">
                <li>
                    <a class="nav-link {{ request()->routeIs('products.index') ? 'active' : 'collapsed' }}"
                        href="{{ route('products.index') }}">
                        <i class="bi bi-circle"></i><span>All Products</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-circle"></i><span>Add New Product</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('inventories.create') }}">
                        <i class="bi bi-circle"></i><span>Add Inventory</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-circle"></i><span>Edit Product</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Product Management Nav -->

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

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#icons-nav" data-bs-toggle="collapse" href="#">
                <i class="ri-truck-line"></i><span>Shipments</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="icons-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li>
                    <a href="#">
                        <i class="bi bi-circle"></i><span>Shipment History</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-circle"></i><span>Deliveries</span>
                    </a>
                </li>
                {{-- <li>
                    <a href="#">
                        <i class="bi bi-circle"></i><span>Boxicons</span>
                    </a>
                </li> --}}
            </ul>
        </li><!-- End Icons Nav -->

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