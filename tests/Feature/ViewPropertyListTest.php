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
    public function it_returns_paginated_property_list_with_10_per_page()
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
