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


        $response = $this->post("/properties/search", [
            'city' => 'vancouver',
            'state' => $washington->id
        ]);

        $response = $response->assertStatus(200)->assertJsonFragment([
           'status' => 'success'
        ])->decodeResponseJson()['properties']['data'];
        $this->assertContains('vancouver', strtolower($response[0]['city']));
        $this->assertContains("{$washington->id}", $response[0]['state_id']);
        $this->assertArrayNotHasKey(1, $response);
    }

    /**
     * @test
     */
    public function it_returns_view_with_all_properties_when_no_query_is_supplied()
    {
        factory(State::class)->create([
            'id' => 1,
            'abbreviation' => 'WA'
        ]);
        factory(Property::class)->create([
            'state_id' => 1
        ]);

        $response = $this->get('/properties/search');

        $response->assertStatus(200);
        $response->assertViewHas('properties');
    }
}
