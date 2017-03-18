<?php

namespace App\Http\Controllers;

use App\Core\Billing\PaymentFailedException;
use App\Core\Property;
use App\Exceptions\AlreadyReservedException;
use App\Core\Billing\PaymentGatewayInterface;
use Illuminate\Http\Request;

class PropertyReservationController extends Controller
{
    protected $paymentGateway;

    function __construct(PaymentGatewayInterface $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store($propertyId)
    {
        if ($user = auth()->user()) {
            $property = Property::available()->findOrFail($propertyId);

            $this->validate(request(), [
                'date_start' => 'required|date',
                'date_end' => 'required|date',
                'payment_token' => 'required'
            ]);

            try {
                $reservation = $property->reserveFor(request('date_start'), request('date_end'), $user);
                $confirmation = $reservation->complete($this->paymentGateway, request('payment_token'));
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

        return response()->json([
            'status' => 'error'
        ], 403);
    }
}
