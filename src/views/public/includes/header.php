<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BookMe - <?php echo (isset($data->pageTitle)) ? htmlentities($data->pageTitle) : 'bookMe'; ?></title>
    <meta name="description" content="BookMe App by Justin Christenson"/>
    <meta name="keywords" content="vacation rental app, php vacation rental, justin christenson, github"/>
    <meta name="copyright" content="Justin Christenson, Vancouver, WA. All Rights Reserved"/>
    <meta name="author" content="Justin Christenson, https://justinc.me"/>
    <meta name="city" content="Vancouver"/>
    <meta name="country" content="US"/>
    <meta name="Distribution" content="Global"/>
    <meta name="Rating" content="General"/>
    <meta name="Robots" content="INDEX,FOLLOW"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/cerulean-theme.css">
    <link rel="stylesheet" href="/css/main.css">
    <meta name="format-detection" content="telephone=no"/>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-nav">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">BookMe</a>
        </div>
        <div class="collapse navbar-collapse" id="main-nav">
            <ul class="nav navbar-nav">
                <li class="<?php echo $data->uri === '' || $data->uri === 'index.php' ? 'active' : null; ?>">
                    <a href="/">Home <span class="sr-only">(current)</span></a>
                </li>
                <li>
                    <a href="https://github.com/justincdotme/bookme" target="_BLANK">GitHub </a>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a><?php echo isset($data->temperature) ? $data->temperature : '-'; ?>&#8457;</a>
                </li>
                <li>
                    <a href="http://justinc.me" target="_BLANK">By Justin Christenson</a>
                </li>
            </ul>
        </div>
    </div>
</nav>