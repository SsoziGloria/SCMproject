<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    @include('user.head')
<<<<<<< Updated upstream
    @stack('styles')

=======


>>>>>>> Stashed changes
</head>

<body>


    @include('user.header')


    <div class="wrapper d-flex flex-column min-vh-100">
<<<<<<< Updated upstream
        <main class="flex-fill" id="main">
=======
        <main class="flex-fill">
>>>>>>> Stashed changes
            @yield('content')
        </main>
        @include('layouts.footer')
    </div>
<<<<<<< Updated upstream
    @stack('scripts')
=======
>>>>>>> Stashed changes
    @include('layouts.scripts')
</body>


</html>