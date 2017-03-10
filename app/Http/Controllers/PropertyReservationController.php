<?php

namespace App\Http\Controllers;

use App\Core\Property;
use App\Core\Reservation;
use App\Core\User;
use App\Exceptions\AlreadyReservedException;
use Illuminate\Http\Request;

class PropertyReservationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store($propertyId)
    {
        $this->validate(request(), [
            'date_start' => 'required|date',
            'date_end' => 'required|date'
        ]);

        if ($user = auth()->user()) {
            $property = Property::available()->findOrFail($propertyId);

            try {
                $reservation = $property->makeReservation($user, request('date_start'), request('date_end'));
                return response()->json([
                    'status' => 'success',
                    'reservation' => $reservation
                ]);
            } catch (AlreadyReservedException $e) {
                return response()->json([
                    'status' => 'error',
                    'msg' => 'The property is unavailable for this date range.'
                ]);
            }
        }

        return response()->json([
            'status' => 'error'
        ], 403);
    }
}
