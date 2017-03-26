<?php

namespace Tests\Feature;

use App\Core\Property\Property;
use App\Core\Property\PropertyImage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewPropertyImageTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function it_returns_paginated_list_of_property_images()
    {
        $this->property = factory(Property::class)->create();
        factory(PropertyImage::class, 20)->create([
            'property_id' => $this->property->id
        ]);

        $response = $this->getJson("/properties/{$this->property->id}/images");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        $this->assertCount(10, $response->decodeResponseJson()['images']['data']);
    }
}