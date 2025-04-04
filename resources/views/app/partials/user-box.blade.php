<div class="user-box text-center">
  <img src="{{ env('APP_PATH') }}/images/users/{{ Auth::user()->user_image }}" alt="user-img" title="{{ Auth::user()->name }}" class="rounded-circle avatar-md" onerror="this.onerror=null;this.src='/images/users/default_user.png';">

  <div class="dropdown">
    <a href="javascript: void(0);" class="text-dark dropdown-toggle h5 mt-2 mb-1 d-block" data-toggle="dropdown">{{ Auth::user()->name }}</a>
    <div class="dropdown-menu user-pro-dropdown">

      <a href="/profile" class="dropdown-item notify-item">
        <i class="fe-user mr-1"></i>
        <span>My Account</span>
      </a>

      {{--<a href="javascript:void(0);" class="dropdown-item notify-item">--}}
      {{--<i class="fe-settings mr-1"></i>--}}
      {{--<span>Settings</span>--}}
      {{--</a>--}}

      {{--<a href="javascript:void(0);" class="dropdown-item notify-item">--}}
      {{--<i class="fe-lock mr-1"></i>--}}
      {{--<span>Lock Screen</span>--}}
      {{--</a>--}}

      <a href="{{ route('logout') }}" class="dropdown-item notify-item"
         onclick="event.preventDefault();document.getElementById('logout-form').submit();">
        <i class="fe-power mr-1 text-danger"></i>
        <span>Logout</span>
      </a>


    </div>
  </div>

  <p class="text-muted">{{ ucfirst(Auth::user()->role) }}</p>

  <h6>{{ date("l, jS F Y") }}</h6>
</div>