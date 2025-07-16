<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('layouts.head')
@stack('styles')

<<<<<<< Updated upstream
=======
<<<<<<< HEAD
@include('layouts.header')

@include('retailer.aside')
>>>>>>> Stashed changes
<body>
    @include('layouts.header')

<<<<<<< Updated upstream
=======
=======
<body>
    @include('layouts.header')

>>>>>>> Stashed changes
    @include('retailer.aside')
    <div class="wrapper d-flex flex-column min-vh-100">
        <main class="flex-fill" id="main">
            @yield('content')
        </main>
        @include('layouts.footer')
    </div>
<<<<<<< Updated upstream
    @stack('scripts')
    @include('layouts.scripts')
=======
    @include('layouts.scripts')
>>>>>>> d2dab711646aed7182ab7947b22aab29e487a426
>>>>>>> Stashed changes
</body>

</html>