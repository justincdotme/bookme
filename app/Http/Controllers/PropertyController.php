<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PropertyController extends Controller
{
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