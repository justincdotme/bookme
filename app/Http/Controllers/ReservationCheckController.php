<?php

namespace App\Http\Controllers;

use App\Core\Property;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationCheckController extends Controller
{
    public function show($propertyId)
    {
        $this->validate(request(), [
            'date_start' => 'required|date',
            'date_end' => 'required|date'
        ]);

        if (Property::find($propertyId)->isAvailableBetween(
                Carbon::parse(request('date_start')), Carbon::parse(request('date_end'))
            )
        ) {
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
