@extends('user.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Welcome, {{ Auth::user()->name }}!</h2>

        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">My Orders</h5>
                        <p class="card-text">View your recent and past orders.</p>
                        <a href="#" class="btn btn-primary btn-sm">View Orders</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Profile</h5>
                        <p class="card-text">Update your account information and password.</p>
                        <a href="{{ route('profile.show') }}" class="btn btn-secondary btn-sm">Edit Profile</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Support</h5>
                        <p class="card-text">Need help? Contact our support team.</p>
                        <a href="#" class="btn btn-outline-info btn-sm">Get Support</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection