@php
// List all route names for User Management group
$userManagementRoutes = [
'users',
'admin.users.byRole',
'admin.users.index',
];
// Check if current route matches any in the group
$isUserManagementActive = false;
foreach ($userManagementRoutes as $route) {
if (request()->routeIs($route) || (request()->routeIs('admin.users.byRole') && in_array(request()->route('role'),
['user', 'retailer', 'supplier', 'admin']))) {
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
// add more if needed
];

$productReviewRoutes = [
'productReviews.index',
'productReviews.create',
'productReviews.edit',
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
break;
}
}

$vManagementRoutes = [
'admin.vendor-validation',
'admin.vendor-validation.history',
];

$analyticsRoutes = [
'analytics',
];
// Check if current route matches any in the group
$isAnalyticsActive = false;
foreach ($analyticsRoutes as $route) {
if (request()->routeIs($route)) {
$isAnalyticsActive = true;
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

        <!--Workforce Management-->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#icons-nav" data-bs-toggle="collapse" href="#">
                <i class="ri-truck-line"></i><span>Workforce Management</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="icons-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('workers.index') }}">
                        <i class="bi bi-circle"></i><span>Workers</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('workforce.index') }}">
                        <i class="bi bi-circle"></i><span>Assigned Tasks</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('workforce.history') }}">
                        <i class="bi bi-circle"></i><span>Task History</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('workforce.unassigned') }}">
                        <i class="bi bi-circle"></i><span>Unassigned Workers</span>
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
            <a class="nav-link {{ request()->routeIs($vManagementRoutes) ? '' : 'collapsed' }}"
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

    </ul>

</aside>