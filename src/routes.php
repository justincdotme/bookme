<?php

use bookMe\lib\Http\Request;
use bookMe\lib\Http\Router;

//Initialize the router
$router = new Router(new Request);

//Public routes
$router->dispatch('GET', '', 'HomeController', 'index');
$router->dispatch('GET', 'index.php', 'HomeController', 'index');
$router->dispatch('GET', 'properties/{id}', 'PropertyController', 'show');
//Admin routes
$router->dispatch('GET', 'admin', 'AdminController', 'index');
$router->dispatch('GET', 'admin/login', 'AdminController', 'login');
$router->dispatch('POST', 'admin/login', 'AdminController', 'login');
$router->dispatch('GET', 'admin/logout', 'AdminController', 'logout');
//Property routes
$router->dispatch('GET', 'admin/properties', 'PropertyController', 'index');
$router->dispatch('GET', 'admin/properties/edit/{id}', 'PropertyController', 'edit');
$router->dispatch('GET', 'admin/property/create', 'PropertyController', 'create');
$router->dispatch('POST', 'admin/properties', 'PropertyController', 'store');
$router->dispatch('GET', 'admin/properties/{id}', 'PropertyController', 'show');
$router->dispatch('PUT', 'admin/properties/{id}', 'PropertyController', 'update');
$router->dispatch('DELETE', 'admin/properties/{id}', 'PropertyController', 'destroy');
//Property image routes
$router->dispatch('POST', 'admin/properties/images/upload', 'PropertyImageController', 'upload');
$router->dispatch('POST', 'admin/properties/images/crop', 'PropertyImageController', 'store');
$router->dispatch('DELETE', 'admin/properties/images/{id}', 'PropertyImageController', 'destroy');
//Reservation routes
$router->dispatch('GET', 'admin/reservations', 'ReservationController', 'index');
$router->dispatch('POST', 'property/reserve', 'ReservationController', 'store');
$router->dispatch('POST', 'property/check', 'ReservationController', 'check');
$router->dispatch('PUT', 'admin/reservations/{id}', 'ReservationController', 'update');
//User routes
$router->dispatch('GET', 'admin/users/edit/{id}', 'UserController', 'edit');
$router->dispatch('GET', 'admin/users', 'UserController', 'index');
$router->dispatch('POST', 'admin/users', 'UserController', 'store');
$router->dispatch('PUT', 'admin/users/{id}', 'UserController', 'update');
$router->dispatch('DELETE', 'admin/users/{id}', 'UserController', 'delete');
$router->dispatch('GET', 'admin/users/create', 'UserController', 'create');
//Catch 404s
$router->catchError();