<?php

namespace Tests\Feature;

use App\Core\Property;
use App\Core\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AddPropertyImageTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function property_image_can_be_uploaded()
    {
        $this->property = factory(Property::class)->create();
        $this->user = factory(User::class)->states(['admin'])->create();

        $response = $this->actingAs($this->user)->post("/properties/{$this->property->id}/photos/upload", [
            'image' => UploadedFile::fake()->image('test-image.png'),
        ]);

        $filePath = $response->decodeResponseJson()['path'];
        $response->assertStatus(201);
        $response->assertJsonFragment([
            'status' => 'success'
        ]);
        $this->assertFileExists($filePath);

        //Cleanup
        File::deleteDirectory(storage_path("app/public/properties"));
    }

    /**
     * @test
     */
    public function image_is_required_for_property_image_upload()
    {
        $this->property = factory(Property::class)->create();
        $this->user = factory(User::class)->states(['admin'])->create();

        $response = $this->actingAs($this->user)->post("/properties/{$this->property->id}/photos/upload", [
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

        $response = $this->actingAs($this->user)->post("/properties/{$this->property->id}/photos/upload", [
            'image' => UploadedFile::fake()->create('test-file.txt'),
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function only_authenticated_admin_users_can_upload_property_images()
    {
        $this->user = $this->user = factory(User::class)->states(['standard'])->create();

        $response = $this->actingAs($this->user)->post("/properties/1/photos/upload", [
            'image' => UploadedFile::fake()->image('test-image.png'),
        ]);

        $response->assertStatus(403);
    }
}