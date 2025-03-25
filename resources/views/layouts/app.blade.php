<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    @include('app.partials.meta')

    @include('app.partials.pre-scripts')
</head>

<body id="app">

<div id="wrapper">

    @include('app.partials.topbar')

    <div class="left-side-menu">
        <div class="slimscroll-menu">

            @include('app.partials.user-box')

            @include('app.partials.menu')

        </div>
    </div>


    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                @auth
                    <input type="hidden" id="id" value="{{ Auth::user()->id }}">
                    <input type="hidden" id="user_name" value="{{ Auth::user()->name }}">
                @endauth

                <input type="hidden" id="token" value="{{ csrf_token() }}">

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>

                @include('app.partials.page-title')

                @include('app.partials.greetings')

                @yield('content')

            </div>

        </div>

        @include('app.partials.footer')

    </div>


</div>

@include('app.partials.post-scripts')

</body>
</html>
