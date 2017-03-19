<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyImageController extends Controller
{
    public function store($propertyId)
    {
        $this->validate(request(), [
            'image' => 'required|mimes:jpeg,png,gif'
        ]);

        $slash = DIRECTORY_SEPARATOR;
        $propertyImagePath = storage_path("app{$slash}public{$slash}properties{$slash}{$propertyId}{$slash}");

        if (!Storage::exists($propertyImagePath)) {
            Storage::makeDirectory($propertyImagePath);
        }

        $file = request()->file('image')->move($propertyImagePath);

        return response()->json([
            'status' => 'success',
            'path' => $file->getPathname()
        ], 201);
    }
}