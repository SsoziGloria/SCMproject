<nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
            <a class="nav-link nav-icon search-bar-toggle " href="#">
                <i class="bi bi-search"></i>
            </a>
        </li><!-- End Search Icon-->


        <!-- Messages Dropdown -->
        <li class="nav-item dropdown">

            <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                <i class="bi bi-chat-left-text"></i>
                <span class="badge bg-success badge-number">3</span>
            </a><!-- End Messages Icon -->

            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li style="min-width: 350px; max-width: 400px; max-height: 500px; overflow-y: auto;">
                    <div style="height: 400px;">
                        <livewire:wirechat.chats widget="true" :allowChatsSearch="false" :showNewChatModalButton="false"
                            :showHomeRouteButton="false" title="Messages" />
                    </div>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li class="dropdown-footer">
                    <a href={{ route('chat.index') }}>Show all messages</a>
                </li>

            </ul> <!-- <-- This was missing! -->
        </li><!-- End Messages Nav -->

        <!-- Profile Dropdown -->
        <li class="nav-item dropdown pe-3">

            <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                @if(auth()->user()->profile_image)
                    <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Profile" class="rounded-circle">
                @else
                    <img src="{{ asset('assets/img/profile-img.jpg') }}" alt="Profile" class="rounded-circle">
                @endif
                <span class="d-none d-md-block dropdown-toggle ps-2">{{ auth()->user()->name }}</span>
            </a><!-- End Profile Image Icon -->

            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                <li class="dropdown-header">
                    <h6>{{ auth()->user()->name }}</h6> @auth @if(auth()->user()->role === 'admin') <span>Admin</span>
                        @elseif(auth()->user()->role === 'supplier')
                            <span>Supplier</span>
                        @elseif(auth()->user()->role === 'retailer')
                            <span>Retailer</span>
                        @else
                            <span>Customer</span>
                        @endif
                    @endauth
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.show') }}">
                        <i class="bi bi-gear"></i>
                        <span>Account Settings</span>
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center" href="pages-faq.html">
                        <i class="bi bi-question-circle"></i>
                        <span>Need Help?</span>
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <a class="dropdown-item d-flex align-items-center" href="#"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Sign Out</span>
                    </a>
                </li>

            </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

    </ul>
</nav>