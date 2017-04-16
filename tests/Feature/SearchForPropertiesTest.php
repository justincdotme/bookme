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

    protected $properties;

    protected function setUp()
    {
        parent::setUp();
        $this->properties = collect([]);
        $this->properties->push(factory(Property::class)->create([
            'state_id' => 1,
            'city' => 'vancouver'
        ]));
        $this->properties->push(factory(Property::class)->create([
            'state_id' => 2,
            'city' => 'portland'
        ]));
    }

    /**
     * @test
     */
    public function user_can_search_for_properties_by_city_and_state()
    {
        $response = $this->json('GET', "/properties/search", [
            'type' => 'city-state',
            'city' => 'vancouver',
            'state' => 1
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => 'success'
        ]);
        $json = $response->decodeResponseJson();
        $this->assertArrayHasKey('properties', $json);
        $this->assertArrayHasKey('query', $json);

    }

    /**
     * @test
     */
    public function it_returns_unfiltered_list_when_no_query_is_supplied()
    {
        $response = $this->json('GET', '/properties/search');

        $response->assertStatus(200);
        $json = $response->decodeResponseJson();
        $this->assertCount(2, $json['properties']['data']);
    }

    /**
     * @test
     */
    public function it_returns_view_for_non_xhr_requests()
    {
        $response = $this->get('/properties/search');

        $response->assertStatus(200);
        $response->assertViewHas('properties');
        $response->assertViewHas('query');
    }
}
