<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
            
        </li><!-- End Dashboard Nav -->
        <li class="nav-heading">Form</li>
        <!-- Add Inventory Nav -->
<li class="nav-item">
    <a href="{{ route('inventories.create') }}" class="nav-link">Add Inventory</a>
</li>

        <!-- End Add Inventory Nav -->

        

        <li class="nav-heading">Pages</li>
<!-- contact us  -->
        <li class="nav-item">
        <a href="/forms/contact.php" class="nav-link">
        <i class="bi bi-envelope"></i> Contact Us
        </a>
        </li>
               <!-- End Contact Us Nav -->

        <!-- profile page nav -->
        <li class="nav-item">
        <a href="{{ route('supplier') }}" class="nav-link">
        <i class="bi bi-truck"></i> Supplier Info
        </a>
</li>
        <!-- End Profile Page Nav -->

    

        
    </ul>

</aside>