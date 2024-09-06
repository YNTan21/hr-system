<header>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}">HR System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasNavbarLabel">HR System</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                        @auth
                            <li class="nav-item dropdown">
                                <a class="nav-link d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{-- <img src="{{asset('/images/profile/default.webp')}}" class="profile-image" alt="Profile Image"> --}}
                                    <span class="navbar username">{{Auth()->user()->username}} <i class="fa-solid fa-angle-down ps-2"></i></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="#">Profile</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{route('dashboard.index')}}">Dashboard</a>
                                </li>
                                <li>
                                    <form action="{{route('auth.logout')}}" method="post">
                                        @csrf 
                                        <button class="dropdown-item">
                                            Logout
                                        </button>
                                    </form>
                                </li>
                                </ul>
                            </li>
                        @endauth
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route ('register') }}">Register</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Login</a>
                            </li>
                        @endguest
                        
                    </ul>
                    <div class="d-block d-xl-none d-lg-none">
                        <x-dashboard.sidebar></x-dashboard.sidebar>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>