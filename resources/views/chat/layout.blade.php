<html>

@include('chat.head')

<body>
    <div class="wrapper d-flex flex-column min-vh-100">
        @include('layouts.header')

        @auth
            @if(auth()->user()->role === 'admin')
                @include('admin.aside')
            @elseif(auth()->user()->role === 'supplier')
                @include('supplier.aside')
            @elseif(auth()->user()->role === 'retailer')
                @include('retailer.aside')
            @else
            @endif
        @endauth

        <main id="main" class="main pt-24 h-[calc(100vh_-_5rem)] tw-chat">
            <livewire:wirechat />
        </main>

        @wirechatAssets
        @livewireScripts
        @vite(['resources/js/app.js']) <!-- Vite JS -->
</body>
@include('layouts.footer')
@include('layouts.scripts')

</html>