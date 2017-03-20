<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PropertyImageUploadController extends Controller
{
    /**
     * Simply uploads a file to the filesystem.
     * The magik happens in PropertyImageController::store()
     *
     * @param $propertyId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($propertyId)
    {
        $this->validate(request(), [
            'image' => 'required|mimes:jpeg,png,gif'
        ]);

        $slash = DIRECTORY_SEPARATOR;
        $propertyImagePath = storage_path("app{$slash}public{$slash}properties{$slash}{$propertyId}{$slash}");
        $file = request()->file('image')->move($propertyImagePath);

        return response()->json([
            'status' => 'success',
            'path' => $file->getPathname()
        ], 201);
    }
}
