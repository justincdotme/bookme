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

class PropertyImageUploadTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function property_image_can_be_uploaded()
    {
        $this->property = factory(Property::class)->create();
        $this->user = factory(User::class)->states(['admin'])->create();
        $file = $this->makeTestUploadFile('1.png', 'png', 'image/png');

        $this->be($this->user);
        $response = $this->call('POST', '/properties/1/photos', [], [], ['image' => $file], ['Accept' => 'application/json']);

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

        $this->be($this->user);
        $response = $this->json('POST', '/properties/1/photos', []);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function non_images_cannot_be_uploaded()
    {
        $this->property = factory(Property::class)->create();
        $this->user = factory(User::class)->states(['admin'])->create();
        $disallowedFile = $this->makeTestUploadFile('test.txt', 'txt', 'text/plain');

        $this->be($this->user);
        $disallowedFileResponse = $this->json('POST', "/properties/{$this->property->id}/photos", ['image' => $disallowedFile]);

        $disallowedFileResponse->assertStatus(422);
    }

    /**
     * @test
     */
    public function only_authenticated_admin_users_can_upload_property_images()
    {
        $this->user = $this->user = factory(User::class)->states(['standard'])->create();
        $this->be($this->user);
        $file = $this->makeTestUploadFile('1.png', 'png', 'image/png');

        $response = $this->call('POST', '/properties/1/photos', [], [], ['image' => $file], ['Accept' => 'application/json']);

        $response->assertStatus(403);
    }

    /**
     * Utility method to create a test file for testing uploads.
     *
     * @param $stubFileName
     * @param $ext
     * @param $mimeType
     * @return UploadedFile
     */
    protected function makeTestUploadFile($stubFileName, $ext, $mimeType)
    {
        $stubFilePath = dirname(__DIR__) . "/stubs/{$stubFileName}";
        $tmpFileName = time() . ".{$ext}";
        $tmpFilePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $tmpFileName;

        copy($stubFilePath, $tmpFilePath);

        return new UploadedFile($tmpFilePath, $tmpFileName, filesize($tmpFilePath), $mimeType, null, true);
    }
}