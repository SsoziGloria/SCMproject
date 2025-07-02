<!DOCTYPE html>
<html lang="en">
@php $user = auth()->user(); @endphp

@if ($user->role === 'user')
    @include('user.head')
    @include('user.header')
@else
    @include('layouts.head')
    @include('layouts.header')
@endif

<body>
    @yield('content')

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Profile</h1>
        </div><!-- End Page Title -->

        <section class="section profile">
            <div class="row">
                <div class="col-xl-4">

                    <div class="card">
                        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

                            @if(auth()->user()->profile_photo)
                                <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Profile"
                                    class="profile-square">
                            @else
                                <img src="{{ asset('assets/img/profile-img.jpg') }}" alt="Profile" class="profile-square">
                            @endif
                            <h2>{{ auth()->user()->name }}</h2>
                            <h3>@auth @if(auth()->user()->role === 'admin')
                                    <span>Admin</span>
                                @elseif(auth()->user()->role === 'supplier')
                                        <span>Supplier</span>
                                    @elseif(auth()->user()->role === 'retailer')
                                        <span>Retailer</span>
                                    @else
                                        <span>Customer</span>
                                    @endif
                            @endauth
                            </h3>
                            <div class="social-links mt-2">
                                <a href="{{ $user->twitter ?? '#'}}" class="twitter"><i class="bi bi-twitter"></i></a>
                                <a href="{{ $user->facebook ?? '#' }}" class="facebook"><i
                                        class="bi bi-facebook"></i></a>
                                <a href="{{ $user->instagram ?? '#' }}" class="instagram"><i
                                        class="bi bi-instagram"></i></a>
                                <a href="{{ $user->linkedin ?? '#' }}" class="linkedin"><i
                                        class="bi bi-linkedin"></i></a>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-xl-8">

                    <div class="card">
                        <div class="card-body pt-3">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            <!-- Bordered Tabs -->
                            <ul class="nav nav-tabs nav-tabs-bordered">

                                <li class="nav-item">
                                    <button class="nav-link active" data-bs-toggle="tab"
                                        data-bs-target="#profile-overview">Overview</button>
                                </li>

                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit
                                        Profile</button>
                                </li>

                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab"
                                        data-bs-target="#profile-settings">Settings</button>
                                </li>

                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab"
                                        data-bs-target="#profile-change-password">Change
                                        Password</button>
                                </li>

                            </ul>
                            <div class="tab-content pt-2">

                                <div class="tab-pane fade show active profile-overview" id="profile-overview">
                                    <h5 class="card-title">About</h5>
                                    <p class="small fst-italic">{{ $user->about ?? 'No bio provided.' }}</p>

                                    <h5 class="card-title">Profile Details</h5>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label ">Full Name</div>
                                        <div class="col-lg-9 col-md-8">{{ $user->name }}</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Country</div>
                                        <div class="col-lg-9 col-md-8">{{ $user->country }}</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Phone</div>
                                        <div class="col-lg-9 col-md-8">{{ $user->phone ?? '-' }}</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Email</div>
                                        <div class="col-lg-9 col-md-8">{{ $user->email }}</div>
                                    </div>
                                    @if (!($user->role === 'user' && $user->hasVerifiedEmail()))
                                        @if ($user->hasVerifiedEmail())
                                            <div class="row">
                                                <div class="col-lg-3 col-md-4 label">Certification Status</div>
                                                <div class="col-lg-9 col-md-8">{{ $user->certification_status }}</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-3 col-md-4 label">Verified</div>
                                            </div>
                                        @else
                                            <form method="POST" action="{{ route('verification.send') }}">
                                                @csrf
                                                <button type="submit" class="btn btn-primary w-100">
                                                    Resend Verification Email
                                                </button>
                                            </form>
                                        @endif

                                    @elseif ($user->role === 'user' && $user->hasVerifiedEmail())
                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label">Verified</div>
                                        </div>
                                    @endif


                                </div>

                                <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                                    <!-- Profile Edit Form -->
                                    <form method="POST" action="{{ route('profile.update') }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="row mb-3">
                                            <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile
                                                Image</label>

                                            <div class="col-md-8 col-lg-9">
                                                @if(auth()->user()->profile_photo)
                                                    <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                                                        alt="Profile" class="profile-square" id="profile-preview">
                                                @else
                                                    <img src="{{ asset('assets/img/profile-img.jpg') }}" alt="Profile"
                                                        class="profile-square">
                                                @endif
                                                <div class="pt-2">
                                                    <label for="profile_photo" class="btn btn-primary btn-sm"
                                                        title="Upload new profile image" style="cursor:pointer;">
                                                        <i class="bi bi-upload"></i>
                                                    </label>
                                                    <input class="form-control" type="file" name="profile_photo"
                                                        id="profile_photo" style="display:none;">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Full
                                                Name</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="fullName" type="text" class="form-control" id="fullName"
                                                    value="{{ old('fullName', $user->name) }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="about" class="col-md-4 col-lg-3 col-form-label">About</label>
                                            <div class="col-md-8 col-lg-9">
                                                <textarea name="about" class="form-control" id="about"
                                                    style="height: 100px">{{ old('about', $user->about) }}</textarea>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="Country"
                                                class="col-md-4 col-lg-3 col-form-label">Country</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="country" type="text" class="form-control" id="Country"
                                                    value="{{ old('country', $user->country) }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="Phone" class="col-md-4 col-lg-3 col-form-label">Phone</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="phone" type="text" class="form-control" id="Phone"
                                                    value="{{ old('phone', $user->phone) }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="email" type="email" class="form-control" id="Email"
                                                    value="{{ old('email', $user->email) }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="Twitter" class="col-md-4 col-lg-3 col-form-label">Twitter
                                                Profile</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="twitter" type="text" class="form-control" id="Twitter"
                                                    value="{{ old('twitter', $user->twitter) }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="Facebook" class="col-md-4 col-lg-3 col-form-label">Facebook
                                                Profile</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="facebook" type="text" class="form-control" id="Facebook"
                                                    value="{{ old('facebook', $user->facebook) }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="Instagram" class="col-md-4 col-lg-3 col-form-label">Instagram
                                                Profile</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="instagram" type="text" class="form-control" id="Instagram"
                                                    value="{{ old('instagram', $user->instagram) }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="Linkedin" class="col-md-4 col-lg-3 col-form-label">Linkedin
                                                Profile</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="linkedin" type="text" class="form-control" id="Linkedin"
                                                    value="{{ old('linkedin', $user->linkedin) }}">
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form><!-- End Profile Edit Form -->


                                    @if(auth()->user()->profile_photo)
                                        <form method="POST" action="{{ route('profile.photo.delete') }}"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm mt-2"
                                                title="Remove my profile image"
                                                onclick="return confirm('Remove profile image?')">
                                                <i class="bi bi-trash"></i> Remove Profile Photo
                                            </button>
                                        </form>
                                    @endif
                                </div>


                                <div class="tab-pane fade pt-3" id="profile-settings">

                                    <!-- Settings Form -->
                                    <form>

                                        <div class="row mb-3">
                                            <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Email
                                                Notifications</label>
                                            <div class="col-md-8 col-lg-9">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="changesMade"
                                                        checked>
                                                    <label class="form-check-label" for="changesMade">
                                                        Changes made to your account
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="newProducts"
                                                        checked>
                                                    <label class="form-check-label" for="newProducts">
                                                        Information on new products and services
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="proOffers">
                                                    <label class="form-check-label" for="proOffers">
                                                        Marketing and promo offers
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="securityNotify"
                                                        checked disabled>
                                                    <label class="form-check-label" for="securityNotify">
                                                        Security alerts
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form><!-- End settings Form -->

                                </div>

                                <div class="tab-pane fade pt-3" id="profile-change-password">
                                    <!-- Change Password Form -->
                                    <form method="POST" action="{{ route('profile.password') }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="row mb-3">
                                            <label for="currentPassword"
                                                class="col-md-4 col-lg-3 col-form-label">Current
                                                Password</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="current_password" type="password" class="form-control"
                                                    id="currentPassword" required>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New
                                                Password</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="new_password" type="password" class="form-control"
                                                    id="newPassword" required>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter
                                                New Password</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="new_password_confirmation" type="password"
                                                    class="form-control" id="renewPassword" required>
                                            </div>
                                        </div>
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul class="mb-0">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary">Change
                                                Password</button>
                                        </div>
                                    </form><!-- End Change Password Form -->

                                </div>

                            </div><!-- End Bordered Tabs -->

                        </div>
                    </div>

                </div>
            </div>
        </section>

    </main><!-- End #main -->

    @include('layouts.footer')

    @include('layouts.scripts')
    <script>
        document.getElementById('profile_photo').addEventListener('change', function (event) {
            const [file] = event.target.files;
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes

            if (file) {
                if (!file.type.startsWith('image/')) {
                    alert('Please select a valid image file.');
                    event.target.value = '';
                    return;
                }
                if (file.size > maxSize) {
                    alert('File is too large. Maximum allowed size is 2MB.');
                    event.target.value = '';
                    return;
                }
                document.getElementById('profile-preview').src = URL.createObjectURL(file);
            }
        });

        const img = document.getElementById('profile-preview');
        img.onload = function () {
            URL.revokeObjectURL(img.src);
        };
    </script>
</body>

</html>