<?php

namespace App\Http\Controllers;

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
