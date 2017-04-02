<?php

namespace Tests\Feature;

use App\Core\Property\Property;
use App\Core\State;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SearchForPropertiesTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * @test
     */
    public function user_can_search_for_properties_by_city_and_state()
    {
        $washington = factory(State::class)->create([
            'id' => 1,
            'abbreviation' => 'WA'
        ]);
        $oregon = factory(State::class)->create([
            'id' => 2,
            'abbreviation' => 'OR'
        ]);
        $vancouverProperty = factory(Property::class)->create([
            'state_id' => 1,
            'city' => 'vancouver'
        ]);
        $portlandProperty = factory(Property::class)->create([
            'state_id' => 2,
            'city' => 'portland'
        ]);


        $response = $this->get("/properties/search?city=vancouver&state={$washington->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
           'status' => 'success'
        ]);
        $response = $response->decodeResponseJson();
        $this->assertContains('vancouver', strtolower($response['properties']['data'][0]['city']));
        $this->assertContains("{$washington->id}", $response['properties']['data'][0]['state_id']);
        $this->assertArrayNotHasKey(1, $response['properties']['data']);
    }

    /**
     * @test
     */
    public function city_is_required_for_property_search()
    {
        $this->response = $this->get("/properties/search?state=1");

        $this->assertFieldHasValidationError('city');
    }

    /**
     * @test
     */
    public function state_is_required_for_property_search()
    {
        $this->response = $this->get("/properties/search?city=vancouver");

        $this->assertFieldHasValidationError('state');
    }

    /**
     * @test
     */
    public function it_returns_paginated_view_with_10_properties_per_page()
    {
        factory(State::class)->create([
            'id' => 1,
            'abbreviation' => 'WA'
        ]);
        factory(Property::class, 11)->create([
            'state_id' => 1
        ]);

        $response = $this->get('/properties');

        $response->assertStatus(200);
        $response = $response->decodeResponseJson();
        $this->assertEquals(2, $response['properties']['last_page']);
        $this->assertEquals(11, $response['properties']['total'], 11);
    }
}
