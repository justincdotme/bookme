<?php

namespace App\Http\Controllers;

use App\Core\Property\Property;
use App\Core\Property\PropertyImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
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

        File::delete($image->getRealPath());

        return response()->json([
            'status' => 'success',
            'full_path' => $propertyImage->full_path,
            'thumb_path' => $propertyImage->thumb_path
        ], 201);
    }

    public function destroy($imageId)
    {
        $image = PropertyImage::find($imageId);

        File::delete($image->full_path);
        File::delete($image->thumb_path);

        $image->delete();

        return response()->json([
          'status' => 'success'
        ]);
    }
}