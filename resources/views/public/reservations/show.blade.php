@extends('public.layouts.public-main')
@section('content')
<h3>Details</h3>
Reserved By: {{ $user->first_name }} {{ $user->last_name }}
Reservation Number: {{ $reservation->id }} <br>
Check In: {{ $reservation->formatted_date_start }} <br>
Check Out: {{ $reservation->formatted_date_end }} <br>
Length of Stay: {{ $reservation->getLengthOfStay() }} days. <br>
Reservation Number: {{ $reservation->id }}. <br>
Reservation Total: {{ $reservation->formatted_amount }}
<h3>Payment</h3>
Card: {{ $charge->getCardLastFour() }}
Card: {{ $charge->getCardBrand() }}
<h3>Property</h3>
Name: {{ $property->name }}<br>
Address: {!!  nl2br($property->formatted_address) !!}<br>
<button>Cancel Reservation</button>
@endsection