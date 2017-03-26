<?php

namespace App\Http\Controllers;

use App\Core\Property\Property;
use Illuminate\Http\Request;

class PropertyImageController extends Controller
{
    /**
     * @param $property
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($property)
    {
        return response()->json([
            'status' => 'success',
            'images' => $property->images()->paginate(10)
        ]);
    }
}
