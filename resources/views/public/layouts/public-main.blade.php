<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>bookMe - Home</title>
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
                <li class="active"><a href="#">Home</a></li>
                <li><a href="#about">Search</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
            <ul id="account-links" class="nav navbar-nav navbar-right blue-border">
                <li><a href="#">Login</a></li>
                <li><a href="#">Signup</a></li>
            </ul>
        </div>
    </div>
</nav>
<div id="content" class="container">
    @yield('content')
    <footer>
        <span>&copy;2017 Justin Christenson<br> All Rights Reserved</span>
    </footer>
</div>
<script src="{{ mix('/js/manifest.js') }}"></script>
<script src="{{ mix('/js/vendor.js') }}"></script>
<script src="{{ mix('/js/app.js') }}"></script>
</body>
</html>