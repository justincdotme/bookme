<?php

namespace App\Http\Controllers\Admin;

use App\Core\Property\Property;
use App\Core\Property\PropertyImage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;

class PropertyImageController extends Controller
{
    protected $imageManager;

    function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    /**
     * @param Property $property
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($property)
    {
        $this->validate(request(), ['image' => 'required|mimes:jpeg,png,gif']);

        $image = request()->file('image')->move(
            config('filesystems.property.image') . $property->id . DIRECTORY_SEPARATOR
        );

        $propertyImage = $property->images()
            ->create([])
            ->setImageManager($this->imageManager)
            ->processUpload(
                $image,
                request()->only(['height', 'width', 'x', 'y'])
            );

        return response()->json([
            'status' => 'success',
            'full_path' => $propertyImage->full_path,
            'thumb_path' => $propertyImage->thumb_path
        ], 201);
    }

    /**
     * @param $property
     * @param $image
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($property, $image)
    {
        File::delete($image->full_path);
        File::delete($image->thumb_path);

        $image->delete();

        return response()->json([
          'status' => 'success'
        ]);
    }
}