<?php

namespace App\Http\Controllers;

use App\Core\Payment\PaymentFailedException;
use App\Core\Property\Property;
use App\Core\Reservation;
use App\Exceptions\AlreadyReservedException;
use App\Core\Payment\PaymentGatewayInterface;
use App\Mail\ReservationCancelled;
use App\Mail\ReservationComplete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{
    /**
     * Create a new property reservation.
     *
     * @param Property $property
     * @return \Illuminate\Http\Response
     */
    public function store($property, PaymentGatewayInterface $paymentGateway)
    {
        $this->authorize('create', Reservation::class);

        $this->validate(
            request(),
            array_merge(
                Reservation::getRules(),
                ['payment_token' => 'required']
            )
        );

        try {
            $user = auth()->user();
            $reservation = $property->reserveFor(request('date_start'), request('date_end'), $user);
            $confirmation = $reservation->complete($paymentGateway, request('payment_token'));
            Mail::send(new ReservationComplete($user, $confirmation, config('mail')));

            return response()->json([
                'status' => 'success',
                'reservation' => $confirmation
            ], 201);
        } catch (PaymentFailedException $e) {
            $reservation->cancel();
            return response()->json([
                'status' => 'error',
                'msg' => 'The payment has failed, please try a different card.'
            ], 422);
        } catch (AlreadyReservedException $e) {
            return response()->json([
                'status' => 'error',
                'msg' => 'The property is unavailable for this date range.'
            ], 422);
        }
    }

    /**
     * @param $property
     * @param $reservation
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($property, $reservation)
    {
        $this->authorize('update', $reservation);

        $reservation->cancel();

        $user = auth()->user();

        Mail::send(new ReservationCancelled($user, $reservation, config('mail')));

        return response()->json([
            'status' => 'success',
            'reservation' => $reservation->fresh()
        ]);
    }
}
