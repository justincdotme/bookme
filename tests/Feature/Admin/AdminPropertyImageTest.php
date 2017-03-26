<?php

namespace Tests\Feature\Admin;

use App\Core\Property\Property;
use App\Core\Property\PropertyImage;
use App\Core\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminPropertyImageTest extends TestCase
{
    use DatabaseMigrations;

    public function tearDown()
    {
        File::deleteDirectory(storage_path("app/public/images/properties/"));
        parent::tearDown();
    }

    /**
     * @test
     */
    public function property_image_can_be_uploaded()
    {
        $this->property = factory(Property::class)->create();
        $this->user = factory(User::class)->states(['admin'])->create();

        $response = $this->actingAs($this->user)->post("/admin/properties/{$this->property->id}/images", [
            'image' => UploadedFile::fake()->image('test-image.png'),
            'height' => 400,
            'width' => 200,
            'x' => 107.4,
            'y' => 129.12
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'status' => 'success'
        ]);
        $response->assertJsonStructure([
            'status',
            'full_path',
            'thumb_path'
        ]);
        $jsonResponse = $response->decodeResponseJson();
        $this->assertFileExists($jsonResponse['full_path']);
        $this->assertFileExists($jsonResponse['thumb_path']);
    }

    /**
     * @test
     */
    public function image_is_required_for_upload()
    {
        $this->property = factory(Property::class)->create();
        $this->user = factory(User::class)->states(['admin'])->create();

        $response = $this->actingAs($this->user)->post("/admin/properties/{$this->property->id}/images", [
            'image1' => UploadedFile::fake()->image('test-image.png'),
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function non_images_cannot_be_uploaded()
    {
        $this->property = factory(Property::class)->create();
        $this->user = factory(User::class)->states(['admin'])->create();

        $response = $this->actingAs($this->user)->post("/admin/properties/{$this->property->id}/images", [
            'image' => UploadedFile::fake()->create('test-file.txt'),
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function non_admin_users_cannot_upload_property_images()
    {
        $this->user = factory(User::class)->states(['standard'])->create();
        $this->property = factory(Property::class)->create();

        $response = $this->actingAs($this->user)->post("/admin/properties/{$this->property->id}/images", [
            'image' => UploadedFile::fake()->image('test-image.png'),
        ]);

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function property_image_can_be_deleted()
    {
        $this->property = factory(Property::class)->create();
        $this->user = factory(User::class)->states(['admin'])->create();
        $testThumb = $this->copyTestImages('thumb');
        $testFull = $this->copyTestImages('full');
        $this->assertFileExists($testFull);
        $this->assertFileExists($testThumb);
        $this->propertyImage = factory(PropertyImage::class)->create([
            'property_id' => $this->property->id,
            'thumb_path' => $testThumb,
            'full_path' => $testFull,
        ]);

        $response = $this->actingAs($this->user)->delete("/admin/properties/{$this->property->id}/images/{$this->propertyImage->id}");

        $response->assertStatus(200);
        $this->assertFileNotExists($testFull);
        $this->assertFileNotExists($testThumb);
    }
}
