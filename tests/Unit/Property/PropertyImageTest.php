<?php

namespace Tests\Unit;

use App\Core\Property\PropertyImage;
use App\Core\Property\Property;
use Intervention\Image\ImageManager;
use Tests\TestCase;
use Illuminate\Support\Facades\File;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PropertyImageTest extends TestCase
{
    use DatabaseMigrations;

    public function tearDown()
    {
        File::deleteDirectory(storage_path("app/public/images/properties"));
        parent::tearDown();
    }

    /**
     * @test
     */
    public function it_generates_cropped_and_resized_image()
    {
        $this->property = factory(Property::class)->create();
        $propertyImage = factory(PropertyImage::class)->create([
            'property_id' => $this->property->id
        ]);
        $testImagePath = $this->copyTestImages();

        $imageFile = $propertyImage->setImageManager(
            app()->make(ImageManager::class)
        )->generateThumbnail($testImagePath);

        $this->assertFileExists($imageFile);
    }

    /**
     * @test
     */
    public function it_generates_thumbnail_image()
    {
        $this->property = factory(Property::class)->create();
        $propertyImage = factory(PropertyImage::class)->create([
            'property_id' => $this->property->id
        ]);
        $testImagePath = $this->copyTestImages();

        $imageFile = $propertyImage->setImageManager(
            app()->make(ImageManager::class)
        )->generateFullSize($testImagePath, 200, 200);

        $this->assertFileExists($imageFile);
    }

    /**
     * Utility method to copy test images to public dir
     *
     * @return string
     */
    protected function copyTestImages()
    {
        $stubPath = dirname(dirname(__DIR__)) . "/stubs/test-1.jpg";
        $tmpName = "{$this->property->id}-original.jpg";
        $tmpDir = storage_path("app/public/images/properties/{$this->property->id}");
        $tmpPath = "{$tmpDir}/{$tmpName}";
        File::makeDirectory($tmpDir, 0775, true, true);
        File::copy($stubPath, $tmpPath);

        return $tmpPath;
    }
}
