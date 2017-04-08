<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\AlreadyReservedException;
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
            'paymentUrl' => config('app.payment.url') . "/{$reservation->charge_id}"
        ]);
    }

    /**
     * @param $property
     * @param $reservation
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($property, $reservation)
    {
        try {
            $reservation->updateReservation(
                request('status'),
                request('amount'),
                request('date_start'),
                request('date_end')
            );
        } catch (AlreadyReservedException $e) {
            return response()->json([
                'status' => 'error',
                'msg' => 'The property has already been reserved for these dates.'
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'reservation' => $reservation->fresh()
        ]);
    }
}
