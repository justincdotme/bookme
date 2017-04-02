<?php

namespace App\Http\Controllers;

use App\Core\Property\Property;
use Illuminate\Http\Request;

class PropertySearchController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function search()
    {
        $this->validate(request(), [
            'city' => 'required_with:state',
            'state' => 'required_with:city'
        ]);

        $properties =  Property::searchCityState(
            request('city'),
            request('state')
        )->paginate(10);

        return response()->json([
            'status' => 'success',
            'properties' => $properties
        ]);
    }
}
