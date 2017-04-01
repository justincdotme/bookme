<?php

namespace App\Http\Controllers;

use App\Core\Reservation;
use Illuminate\Http\Request;

class UserReservationController extends Controller
{
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
}
