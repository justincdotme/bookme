<?php

namespace App\Http\Controllers;

use App\Core\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('public.properties.show', [
            'property' => Property::available()->findOrFail($id)
        ]);
    }

    public function store(Property $property)
    {
        $this->validate(request(), $property->rules);
        $newProperty = Property::create(request()->except('id'));
        //TODO - Handle photo upload
        return response()->json([
            'status' => 'success',
            'property_id' => $newProperty->id
        ], 201);
    }
}