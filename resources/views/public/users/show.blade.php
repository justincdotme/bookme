@extends('public.layouts.public-main')
@section('content')
<h1>Account for {{ $user->email }}</h1>
<strong>Name:</strong> {{ $user->first_name }} {{ $user->last_name }} <br>
@endsection