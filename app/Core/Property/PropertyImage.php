<?php

namespace App\Core\Property;

use Illuminate\Database\Eloquent\Model;

class PropertyImage extends Model
{
    protected $guarded = [];
    protected $extension;
    protected $imageManager;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * @param $image
     * @param $imageData
     * @return null|static
     */
    public function processUpload($image, $imageData)
    {
        $this->extension = $image->guessExtension();
        $fullPath = $this->generateFullSize(
            $image->getPathname(),
            $imageData['height'],
            $imageData['width']
        );

        $thumbPath = $this->generateThumbnail($fullPath);

        $this->update([
            'full_path' => $fullPath,
            'thumb_path' => $thumbPath,
        ]);

        return $this;
    }

    /**
     * Generate a cropped, resized image.
     *
     * @param $imagePath
     * @param $height
     * @param $width
     * @return mixed
     */
    public function generateFullSize($imagePath, $height, $width)
    {
        $path = $this->generateImagePath('full', $imagePath);
        $this->imageManager
            ->make($imagePath)
            ->resize($height, $width)
            ->save($path);

        return $path;
    }

    /**
     * Generate a thumbnail from a cropped and resized image.
     *
     * @param $imagePath
     * @return mixed
     */
    public function generateThumbnail($imagePath)
    {
        $path = $this->generateImagePath('thumb', $imagePath);
        $this->imageManager
            ->make($imagePath)
            ->resize(125, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($path);

        return $path;
    }

    /**
     * Set the image manager used for transformations.
     *
     * @param $imageManager
     * @return $this
     */
    public function setImageManager($imageManager)
    {
        $this->imageManager = $imageManager;
        return $this;
    }

    /**
     * Helper method to generate the image storage path.
     *
     * @return string
     */
    protected function generateImagePath($type, $file)
    {
        if (null === $this->extension) {
            $this->extension = (new \SPLFileInfo($file))->getExtension();
        }

        return config('filesystems.property.image') .
            $this->property->id .
            DIRECTORY_SEPARATOR .
            $this->id .
            "-{$type}.{$this->extension}";
    }
}
