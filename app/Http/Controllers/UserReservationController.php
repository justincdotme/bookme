<?php

namespace App\Http\Controllers;

use App\Core\Payment\PaymentGatewayInterface;
use Illuminate\Http\Request;

class UserReservationController extends Controller
{
    protected $paymentGateway;

    /**
     * PropertyReservationController constructor.
     * @param PaymentGatewayInterface $paymentGateway
     */
    function __construct(PaymentGatewayInterface $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    /**
     * @param $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($user)
    {
        if (auth()->user()->id !== $user->id) {
            abort(403);
        }
        return response()->json([
            'status' => 'success',
            'reservations' => $user->reservations
        ]);
    }

    /**
     * @param $user
     * @param $reservation
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($user, $reservation)
    {
        $this->authorize('view', $reservation);

        return view('public.reservations.show', [
            'property' => $reservation->property,
            'reservation' => $reservation,
            'user' => $reservation->user,
            'charge' => $this->paymentGateway->getChargeById($reservation->charge_id)
        ]);
    }
}
