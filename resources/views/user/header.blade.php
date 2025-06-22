<header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
        <a href={{ route('dashboard') }} class="logo d-flex align-items-center">
            <img src="assets/img/logo.png" alt="">
            <span class="d-none d-lg-block">ChocolateSCM</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
            <li class="nav-item">
                <a class="nav-link" href="#">Product</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Order</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Track</a>
            </li>
        </ul>
    </nav>

    @include('layouts.nav')<!-- End Icons Navigation -->

</header>