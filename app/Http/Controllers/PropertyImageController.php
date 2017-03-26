<?php

namespace App\Http\Controllers;

use App\Core\Property\Property;
use Illuminate\Http\Request;

class PropertyImageController extends Controller
{
    /**
     * @param $propertyId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($propertyId)
    {
        return response()->json([
            'status' => 'success',
            'images' => Property::find($propertyId)->images()->paginate(10)
        ]);
    }
}
