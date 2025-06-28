<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

@include('layouts.head')


</head>

<body>


@include('layouts.header')

@include('layouts.aside')

<main id="main" class="main">
    @yield('content')
</main>

@include('layouts.footer')
@include('layouts.scripts')
</body>

</html>