<?php

namespace App\Http\Controllers;

use App\Core\Property\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Show a property
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

    /**
     * Store a newly created property.
     *
     * @param Property $property
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Property $property)
    {
        $this->validate(request(), $property->rules);
        $newProperty = Property::create(request()->except('id'));

        return response()->json([
            'status' => 'success',
            'property_id' => $newProperty->id
        ], 201);
    }

    /**
     * Updated an existing property.
     *
     * @param $propertyId
     * @param Property $property
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($propertyId, Property $property)
    {
        $this->validate(request(), $property->rules);
        $property->find($propertyId)->update(request()->except('id'));

        return response()->json([
            'status' => 'success'
        ]);
    }
}