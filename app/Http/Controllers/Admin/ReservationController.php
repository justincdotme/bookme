<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReservationController extends Controller
{
    public function show($property, $reservation)
    {
        return response()->json([
            'status' => 'success',
            'reservation' => $reservation,
            'property' => $property,
            'user' => $reservation->user,
            'paymentUrl' => config('app.payment.url') . DIRECTORY_SEPARATOR . $reservation->charge_id
        ]);
    }
}
