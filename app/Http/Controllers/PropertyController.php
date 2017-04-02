<?php

namespace App\Http\Controllers;

use App\Core\Property\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'properties' => Property::paginate(10)
        ]);
    }
    /**
     * Show a property
     *
     * @param  $property
     * @return \Illuminate\Http\Response
     */
    public function show($property)
    {
        return view('public.properties.show', [
            'property' => $property,
            'images' => $property->images()->paginate(10)
        ]);
    }
}