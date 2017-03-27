<?php

namespace App\Http\Controllers;

use App\Core\Payment\PaymentFailedException;
use App\Core\Property\Property;
use App\Core\Reservation;
use App\Exceptions\AlreadyReservedException;
use App\Core\Payment\PaymentGatewayInterface;
use App\Mail\ReservationComplete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PropertyReservationController extends Controller
{
    protected $paymentGateway;

    function __construct(PaymentGatewayInterface $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function show($property, $reservation)
    {
        $this->authorize('view', $reservation);
        return view('public.reservations.show', [
            'property' => $property,
            'reservation' => $reservation,
            'user' => null
        ]);
    }

    /**
     * Create a new property reservation.
     *
     * @param Property $property
     * @return \Illuminate\Http\Response
     */
    public function store($property)
    {
        $this->authorize('create', Reservation::class);
        $this->validate(
            request(),
            array_merge(
                Reservation::getRules(),
                ['payment_token' => 'required']
            )
        );
        $user = auth()->user();

        try {
            $reservation = $property->reserveFor(request('date_start'), request('date_end'), $user);
            $confirmation = $reservation->complete($this->paymentGateway, request('payment_token'));
            Mail::to($user)->send(new ReservationComplete($user, $confirmation, config('mail')));

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

        return response()->json([
            'status' => 'error'
        ], 403);
    }
}
