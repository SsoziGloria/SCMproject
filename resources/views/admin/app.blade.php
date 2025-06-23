<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('layouts.head')

@include('layouts.header')

@include('admin.aside')

<main id="main" class="main">
    @yield('content')
</main>

@include('layouts.footer')
@include('layouts.scripts')


</html>