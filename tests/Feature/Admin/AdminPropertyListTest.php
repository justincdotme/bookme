<?php

namespace Tests\Feature\Admin;

use App\Core\Property\Property;
use App\Core\Property\PropertyImage;
use App\Core\State;
use App\Core\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminPropertyListTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function it_returns_paginated_view_with_10_properties_per_page()
    {
        $this->disableExceptionHandling();
        $properties = factory(Property::class, 20)->make();
        $this->user = factory(User::class)->states(['admin'])->create();
        $state = factory(State::class)->create([
            'abbreviation' => 'WA'
        ]);
        $properties->each(function ($item, $key) use ($state) {
            $item->state()->associate($state);
            $item->save();
        });

        $response = $this->actingAs($this->user)->get('/admin/properties');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        $this->assertEquals(
            10,
            count($response->decodeResponseJson()['properties']['data'])
        );
    }
}