<header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
        <a href={{ route('dashboard') }} class="logo d-flex align-items-center">
            <img src="{{ asset('assets/img/logo.png') }}" alt="">
            <span class="d-none d-lg-block">ChocolateSCM</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <div class="search-bar">
        <form class="search-form d-flex align-items-center" method="GET" action="{{ route('search') }}">
            <input type="text" name="query" placeholder="Search" title="Enter search keyword"
                value="{{ request('query') }}">
            <button type="submit" title="Search"><i class="bi bi-search"></i></button>
        </form>
    </div><!-- End Search Bar -->

    @include('layouts.nav')<!-- End Icons Navigation -->

</header>