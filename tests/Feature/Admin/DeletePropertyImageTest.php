<?php

namespace Tests\Feature\Admin;

use App\Core\Property\Property;
use App\Core\Property\PropertyImage;
use App\Core\User;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DeletePropertyImageTest extends TestCase
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
    public function admin_users_can_delete_property_images()
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

    /**
     * @test
     */
    public function non_admin_users_cannot_delete_property_images()
    {
        $this->property = factory(Property::class)->create();
        $this->user = factory(User::class)->states(['standard'])->create();
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

        $response->assertStatus(403);
        $this->assertFileExists($testFull);
        $this->assertFileExists($testThumb);
    }
}
