<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<script>
    window.bookMe = {
        csrfToken: "{!! csrf_token() !!}"
    };
</script>
<nav class="navbar navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">
                <img alt="bookMe" src="/images/logo.png">
            </a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul id="main-navigation" class="nav navbar-nav">
                <li{{ request()->is("/") ? ' class=active ' : '' }}><a href="/">Home</a></li>
                <li{{ request()->is("properties/search") ? ' class=active ' : '' }}><a href="/properties/search">Search</a></li>
                <li{{ request()->is("contact") ? ' class=active ' : '' }}><a href="/contact">Contact</a></li>
            </ul>
            <ul id="account-links" class="nav navbar-nav navbar-right blue-border">
                @if (!Auth::check())
                    <li{{ request()->is("login") ? ' class=active ' : '' }}><a href="/login">Login</a></li>
                    <li{{ request()->is("register") ? ' class=active ' : '' }}><a href="/register">Register</a></li>
                @else
                    <li><a href="#">My Account</a></li>
                @endif
            </ul>
        </div>
    </div>
</nav>
<div id="content" class="container">
    @yield('content')
</div>
<div class="container">
    <footer>
        <span>&copy;2017 Justin Christenson<br> All Rights Reserved</span>
    </footer>
</div>
<script src="{{ mix('/js/manifest.js') }}"></script>
<script src="{{ mix('/js/vendor.js') }}"></script>
<script src="{{ mix('/js/app.js') }}"></script>
</body>
</html>