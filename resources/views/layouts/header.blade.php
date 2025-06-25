<header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
    <a href="{{ route('dashboard') }}">Home</a>   
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