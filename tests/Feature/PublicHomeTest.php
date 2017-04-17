<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PublicHomeTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function users_can_view_application_home_page()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function homepage_contains_featured_properties()
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        $response->assertViewHas('featuredProperties');
    }

    /**
     * @test
     */
    public function homepage_contains_list_of_states_for_search()
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        $response->assertViewHas('states');
    }
}
