<div class="navbar-custom">
    <ul class="list-unstyled topnav-menu float-right mb-0">

        @include('app.partials.search-bar')

        <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                {{--<img src="/images/users/{{ Auth::user()->user_image }}" alt="user-image" class="rounded-circle" onerror="this.onerror=null;this.src='/images/users/default_user.png';">--}}
                <span class="pro-user-name text-white ml-1">
                    {{ Str::words(Auth::user()->name, 2, '') }}
                    <i class="mdi mdi-chevron-down"></i>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right profile-dropdown ">

                <div class="dropdown-header noti-title">
                    <h6 class="text-overflow m-0">Welcome !</h6>
                </div>

                <a href="{{ route('profile') }}" class="dropdown-item notify-item">
                    <i class="fe-user"></i>
                    <span>Profile</span>
                </a>

                {{--<a href="javascript:void(0);" class="dropdown-item notify-item">--}}
                {{--<i class="fe-settings"></i>--}}
                {{--<span>Settings</span>--}}
                {{--</a>--}}

                {{--<a href="javascript:void(0);" class="dropdown-item notify-item">--}}
                {{--<i class="fe-lock"></i>--}}
                {{--<span>Lock Screen</span>--}}
                {{--</a>--}}

                <div class="dropdown-divider"></div>

                <a href="{{ route('logout') }}" class="dropdown-item notify-item"
                   onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    <i class="fe-power mr-1 text-danger"></i>
                    <span>Logout</span>
                </a>

            </div>
        </li>

    </ul>

    @include('app.partials.logo')

    <ul class="list-unstyled topnav-menu topnav-menu-left m-0">

        <li>
            <button class="button-menu-mobile waves-effect waves-light">
                <i class="fe-menu"></i>
            </button>
        </li>

        <li class="dropdown notification-list">
            <a id="clock_span" class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                <span class="pro-user-name ml-1" id="clock"></span>
            </a>

            <div class="dropdown-menu dropdown-menu-right">
                <div data-provide="datepicker-inline"></div>
            </div>
        </li>

        <li class="dropdown d-none d-lg-block ml-2 bg-dark">
            <a class="nav-link dropdown-toggle waves-effect waves-light" href="javascript:void(0);">
                <strong class="text-warning font-16">Notes Keeper</strong>
            </a>
        </li>

    </ul>
</div>
