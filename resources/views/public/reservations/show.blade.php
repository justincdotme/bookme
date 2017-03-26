<h3>Details</h3>
Reservation Number: {{ $reservation->id }} <br>
Check In: {{ $reservation->formatted_date_start }} <br>
Check Out: {{ $reservation->formatted_date_end }} <br>
Length of Stay: {{ $reservation->getLengthOfStay() }} days. <br>
Reservation Number: {{ $reservation->id }}. <br>
Reservation Total: {{ $reservation->formatted_amount }}
<h3>Property</h3>
Name: {{ $property->name }}<br>
Address: {!!  nl2br($property->formatted_address) !!}<br>
<button>Cancel Reservation</button>