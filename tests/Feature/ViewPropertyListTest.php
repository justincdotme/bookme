<?php

namespace Tests\Feature;

use App\Core\Property\Property;
use App\Core\State;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewPropertyListTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function it_returns_paginated_view_with_10_properties_per_page()
    {
        $properties = factory(Property::class, 20)->make();
        $state = factory(State::class)->create([
            'abbreviation' => 'WA'
        ]);

        $properties->each(function ($item, $key) use ($state) {
            $item->state()->associate($state);
            $item->save();
        });

        $response = $this->get('/properties/');

        $response->assertStatus(200);
        $response->assertViewHas('properties');
        $response->assertSee('/properties?page=2');
        $response->assertDontSee('/properties?page=3');
    }
}