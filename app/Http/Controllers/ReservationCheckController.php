<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class ReservationCheckController extends Controller
{
    /**
     * Check if a property is reserved for a specific data.
     *
     * @param $property
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($property)
    {
        $this->validate(request(), [
            'date_start' => 'required|date',
            'date_end' => 'required|date'
        ]);

        if ($property->isAvailableBetween(Carbon::parse(request('date_start')), Carbon::parse(request('date_end')))) {
            return response()->json([
                'status' => 'success',
                'msg' => 'The property is available for this date range.'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'msg' => 'The property is unavailable for this date range.'
        ]);
    }
}
