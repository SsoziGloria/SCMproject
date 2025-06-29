@php
    $user = auth()->user();
    $layout = ($user && $user->role === 'user') ? 'user.app' : 'layouts.app';
@endphp

@extends($layout)

@section('title', 'Verify Email')
@section('content')
    <main class="main">
        <div class="max-w-md mx-auto mt-12 p-6 bg-white rounded shadow">
            <h2 class="text-xl font-semibold mb-4">Verify Your Email</h2>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-4 text-green-600">
                    A new verification link has been sent to your email address.
                </div>
            @endif

            <p class="mb-4">Please check your email to verify your address before continuing.</p>

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary w-100">
                    Resend Verification Email
                </button>
            </form>
        </div>
    </main>
@endsection