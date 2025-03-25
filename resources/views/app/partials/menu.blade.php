<div id="sidebar-menu">

    <ul class="metismenu" id="side-menu">



        <li class="menu-title"><i class="fas fa-university font-18 mr-1"></i>Navigation</li>

        <li><a href="{{ route('dashboard') }}"><i class="mdi mdi-view-dashboard"></i><span>Dashboard</span></a></li>

        <li>
            <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                <i class="fe-power mr-1 text-danger"></i>
                <span>Logout</span>
            </a>
        </li>

    </ul>

</div>

<div class="clearfix"></div>
