<?php

namespace App\Http\Controllers;

use App\Core\Property\Property;
use App\Events\PropertyImageUploadProcessed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;

class PropertyImageController extends Controller
{
    /**
     * @param $propertyId
     * @param Property $property
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($propertyId, Property $property)
    {
        $this->validate(request(), [
            'image' => 'required|mimes:jpeg,png,gif'
        ]);

        $image = request()->file('image')->move(
            config('filesystems.property.image') . $propertyId . DIRECTORY_SEPARATOR
        );
        $imageData = request()->only([
            'height',
            'width',
            'x',
            'y'
        ]);

        $propertyImage = $property->find($propertyId)->makeImage();

        $propertyImage->setImageManager(
            app()->make(ImageManager::class)
        )->processUpload($image, $imageData);

        //TODO - Use Queue worker for this event.
        event(new PropertyImageUploadProcessed($image->getRealPath()));

        return response()->json([
            'status' => 'success',
            'full_path' => $propertyImage->full_path,
            'thumb_path' => $propertyImage->thumb_path
        ], 201);
    }
}