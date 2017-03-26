<?php

namespace App\Http\Controllers\Admin;

use App\Core\Property\Property;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PropertyController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'properties' => Property::available()->paginate(10)
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
