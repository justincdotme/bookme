<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReservationController extends Controller
{
    /**
     * @param $property
     * @param $reservation
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @param $property
     * @param $reservation
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($property, $reservation)
    {
        $reservation->updateReservation(request('status'), request('amount'));

        return response()->json([
            'status' => 'success',
            'reservation' => $reservation->fresh()
        ]);
    }
}
