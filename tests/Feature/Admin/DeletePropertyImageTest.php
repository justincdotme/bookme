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

/**
 * @group filesystem
 */

class DeletePropertyImageTest extends TestCase
{
    use DatabaseMigrations;

    protected $testThumb;
    protected $testFull;

    protected function setUp()
    {
        parent::setUp();
        $this->property = factory(Property::class)->create();
        $this->testThumb = $this->copyTestImages('thumb');
        $this->testFull = $this->copyTestImages('full');
    }

    public function tearDown()
    {
        File::deleteDirectory(storage_path("app/public/images/properties/"));
        parent::tearDown();
    }

    /**
     * @test
     */
    public function non_admin_users_cannot_delete_property_images()
    {
        $this->user = factory(User::class)->states(['standard'])->create();
        $this->propertyImage = factory(PropertyImage::class)->create([
            'property_id' => $this->property->id,
            'thumb_path' => $this->testThumb,
            'full_path' => $this->testFull,
        ]);

        $response = $this->actingAs($this->user)->delete("/admin/properties/{$this->property->id}/images/{$this->propertyImage->id}");

        $response->assertStatus(403);
        $this->assertFileExists($this->testThumb);
        $this->assertFileExists($this->testFull);
    }

    /**
     * @test
     */
    public function admin_users_can_delete_property_images()
    {
        $this->user = factory(User::class)->states(['admin'])->create();
        $this->propertyImage = factory(PropertyImage::class)->create([
            'property_id' => $this->property->id,
            'thumb_path' => $this->testThumb,
            'full_path' => $this->testFull,
        ]);

        $response = $this->actingAs($this->user)
            ->delete("/admin/properties/{$this->property->id}/images/{$this->propertyImage->id}");

        $response->assertStatus(200);
        $this->assertFileNotExists($this->testThumb);
        $this->assertFileNotExists($this->testFull);
    }
}
