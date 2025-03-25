<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('app.partials.meta')

    <link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="/css/app.min.css" rel="stylesheet" type="text/css" />

</head>

<body class="authentication-bg authentication-bg-pattern">

<div class="account-pages mt-5 mb-5">
    @yield('content')
</div>

<script src="/js/vendor.min.js"></script>
<script src="/js/app.min.js"></script>
<script src="/js/application.js"></script>

</body>
</html>
