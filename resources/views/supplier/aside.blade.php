<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li><!-- End Dashboard Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
                <i class="ri-shopping-bag-2-line"></i><span>Product Management</span><i
                    class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li>
                    <a href="#">
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
            <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
                <i class="ri-inbox-unarchive-line"></i><span>Orders</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('orders.incoming') }}">
                        <i class="bi bi-circle"></i><span>Incoming Orders</span>
                    </a>
                </li>
                <li>
                    <a href="">
                        <i class="bi bi-circle"></i><span>Order Details</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-circle"></i><span>Reject Orders</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Orders Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
                <i class="ri-truck-line"></i><span>Shipments</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li>
                    <a href="tables-general.html">
                        <i class="bi bi-circle"></i><span>Shipment History</span>
                    </a>
                </li>
                <li>
                    <a href="tables-data.html">
                        <i class="bi bi-circle"></i><span>Delivery Status</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Shipment Nav -->

        <li class="nav-heading">Communication</li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="#">
                <i class="bi bi-chat-left-quote"></i>
                <span>Chat</span>
            </a>
        </li><!-- End Chat Page Nav -->

        <li class="nav-heading">Account</li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('profile.show') }}">
                <i class="bi bi-person"></i>
                <span>Profile</span>
            </a>
        </li><!-- End Profile Page Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" href="#">
                <i class="bi bi-question-circle"></i>
                <span>F.A.Q</span>
            </a>
        </li><!-- End F.A.Q Page Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" href=#">
                <i class="bi bi-envelope"></i>
                <span>Contact</span>
            </a>
        </li><!-- End Contact Page Nav -->

    </ul>

</aside>