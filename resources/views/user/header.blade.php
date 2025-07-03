<header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
        <a href={{ route('dashboard') }} class="logo d-flex align-items-center">
            <img src="{{ asset('assets/img/logo.png') }}" alt="">
            <span class="d-none d-lg-block">ChocolateSCM</span>
        </a>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
            <li class="nav-item">
                <a class="nav-link nav-icon" href="{{ route('shop.index') }}"
                    style="display: flex; flex-direction: row; align-items: center;">
                    <i class="ri-shopping-bag-2-line"></i>
                    <h4 style="margin-bottom: 0;">Shop</h4>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link nav-icon" href="{{ route('cart.index') }}"
                    style="display: flex; flex-direction: row; align-items: center;">
                    <i class="ri-shopping-cart-2-line"></i>
                    <h4 style="margin-bottom: 0;">Cart</h4>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link nav-icon" href="#" style="display: flex; flex-direction: row; align-items: center;">
                    <i class="ri-funds-box-line"></i>
                    <h4 style="margin-bottom: 0;">Orders</h4>
                </a>
            </li>
        </ul>
    </nav>

    @include('user.nav')<!-- End Icons Navigation -->

</header>