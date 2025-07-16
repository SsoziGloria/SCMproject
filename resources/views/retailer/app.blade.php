<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('layouts.head')
@stack('styles')

@include('layouts.header')

@include('retailer.aside')

<body>
    @include('layouts.header')

    @include('retailer.aside')
    <div class="wrapper d-flex flex-column min-vh-100">
        <main class="flex-fill" id="main">
            @yield('content')
        </main>
        @include('layouts.footer')
    </div>
    @stack('scripts')
    @include('layouts.scripts')
</body>

</html>