<?php

namespace App\Http\Controllers;

use App\Core\BillingAddress as Address;
use App\Core\Payment\PaymentFailedException;
use App\Core\Property\Property;
use App\Core\Reservation;
use App\Core\State;
use App\Exceptions\AlreadyReservedException;
use App\Core\Payment\PaymentGatewayInterface;
use App\Mail\ReservationCancelled;
use App\Mail\ReservationComplete;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{
    /**
     * @param $property
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($property)
    {
        return view('public.reservations.create', [
            'property' => $property,
            'stateList' => State::getList()
        ]);
    }

    /**
     * Create a new property reservation.
     *
     * @param Property $property
     * @return \Illuminate\Http\Response
     */
    public function store($property, PaymentGatewayInterface $paymentGateway)
    {
        $this->validate(
            request(),
            array_merge(
                Reservation::getRules(),
                ['payment_token' => 'required'],
                Address::getRules()
            )
        );

        try {
            $user = auth()->user();
            $reservation = $property->reserveFor(request('date_start'), request('date_end'), $user);
            $billingAddress = Address::create(
                request()->only([
                    'line1',
                    'line2',
                    'city',
                    'state_id',
                    'zip',
                ])
            );
            $confirmation = $reservation->complete($paymentGateway, request('payment_token'), $billingAddress);
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

        Mail::to(config('mail.accounts.admin.to'))
            ->send(new ReservationCancelled($user, $reservation));

        return response()->json([
            'status' => 'success',
            'reservation' => $reservation->fresh()
        ]);
    }
}
