<!DOCTYPE html>
<html lang="en">
@php $user = auth()->user(); @endphp

@if ($user->role === 'user')
    @include('user.head')

    <body>
        @include('user.header')
@elseif ($user->role === 'supplier')
        @include('layouts.head')

        <body>
            @include('layouts.header')
            @include('supplier.aside')
    @elseif ($user->role === 'retailer')
            @include('layouts.head')

            <body>
                @include('layouts.header')
                @include('retailer.aside')
        @elseif ($user->role === 'admin')
                @include('layouts.head')

                <body>
                    @include('layouts.header')
                    @include('admin.aside')
            @else
                    @include('layouts.head')

                    <body>
                        @include('layouts.header')
                        @include('layouts.aside')
                @endif

                    <main id="main" class="main pt-24 h-[calc(100vh_-_5rem)] tw-chat">
                        <livewire:wirechat />


                        @wirechatAssets
                        @livewireScripts
                        @vite(['resources/js/app.js']) <!-- Vite JS -->
                    </main>
                    @include('layouts.footer')
                    @include('layouts.scripts')
                </body>

</html>