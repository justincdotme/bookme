<?php

namespace App\Http\Controllers\Admin;

use App\Core\Property\Property;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PropertyController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'properties' => Property::withoutGLobalScopes()->paginate(10)
        ], 200);

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

        $propertyId = $property->create(request()->except('id'))->id;

        return response()->json([
            'status' => 'success',
            'property_id' => $propertyId
        ], 201);
    }

    /**
     * Updated an existing property.
     *
     * @param Property $property
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($property)
    {
        $this->validate(request(), $property->rules);

        $property->update(request()->except('id'));

        return response()->json([
            'status' => 'success'
        ]);
    }
}
