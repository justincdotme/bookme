<?php

namespace Tests\Unit;

use App\Core\Property\PropertyImage;
use App\Core\Property\Property;
use Intervention\Image\ImageManager;
use Tests\TestCase;
use Illuminate\Support\Facades\File;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * @group filesystem
 */

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
}
