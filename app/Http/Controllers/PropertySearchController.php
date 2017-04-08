<?php

namespace App\Http\Controllers;

use App\Core\Property\PropertySearch;
use Illuminate\Http\Request;

class PropertySearchController extends Controller
{
    /**
     * @param PropertySearch $propertySearch
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(PropertySearch $propertySearch)
    {
        $properties = $propertySearch
            ->setType(request('searchType'))
            ->search(request()->toArray())
            ->getResults(10);

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'properties' => $properties
            ]);
        }

        return view('public.properties.search', [
            'properties' => $properties
        ]);
    }
}