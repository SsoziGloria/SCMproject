<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('user.head')

@include('user.header')

<main id="main" class="main">
    @yield('content')
</main>

@include('layouts.footer')
@include('layouts.scripts')


</html>