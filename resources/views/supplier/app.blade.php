<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('layouts.head')
@stack('styles')

<body>
    @include('layouts.header')

    @include('supplier.aside')
    <div class="wrapper d-flex flex-column min-vh-100">
        <main id="main" class="flex-fill">
            @yield('content')
        </main>
        @include('layouts.footer')
    </div>
    @stack('scripts')
    @include('layouts.scripts')
</body>

</html>