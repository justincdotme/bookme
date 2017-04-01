<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserReservationController extends Controller
{
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
            'user' => null
        ]);
    }
}
