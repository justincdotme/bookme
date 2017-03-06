<?php

namespace App\Http\Controllers;

use App\Core\Property;
use App\Core\Reservation;
use App\Core\User;
use Illuminate\Http\Request;

class PropertyReservationController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth', 'interstitial']);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store($propertyId)
    {
        //TODO - Validate the request
        if ($user = auth()->user()) {
            $property = Property::available()->findOrFail($propertyId);
            $reservation = $property->makeReservation($user, request('date_start'), request('date_end'), new Reservation);

            return response()->json([
                'status' => 'success',
                'reservation' => $reservation
            ]);
        }

        return response()->json([
            'status' => 'error'
        ]);
    }
}
