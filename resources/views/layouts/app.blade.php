<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('layouts.head')

<body>
    @yield('content')
    @include('layouts.scripts')
</body>

</html>