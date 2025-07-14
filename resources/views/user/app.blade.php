<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    @include('user.head')
    @stack('styles')

</head>

<body>


    @include('user.header')


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