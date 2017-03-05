<?php

namespace Tests\Feature;

use App\Core\Property;
use App\Core\State;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewPropertyTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * @test
     */
    public function user_can_view_a_property()
    {
        $property = factory(Property::class)->make([
            'name' => 'Beach House',
            'rate' => 123.45,
            'short_description' => 'Test short description',
            'long_description' => 'This is a test long description',
            'street_address_line_1' => '1234 Any St',
            'street_address_line_2' => 'Apt. B',
            'city' => 'Vancouver',
            'zip' => 12345
        ]);

        $state = factory(State::class)->create([
            'abbreviation' => 'WA'
        ]);

        $property->state()->associate($state);
        $property->save();

        $response = $this->get('/properties/' . $property->id);

        $response->assertStatus(200);
        $response->assertSee('Beach House');
        $response->assertSee('Rate: $123.45');
        $response->assertSee('Test short description');
        $response->assertSee('This is a test long description');
        $response->assertSee('1234 Any St');
        $response->assertSee('Apt. B');
        $response->assertSee('Vancouver, WA 12345');
    }

    /**
     * @test
     */
    public function user_cannot_view_unavailable_property()
    {
        $property = factory(Property::class)->states(['unavailable'])->make();

        $response = $this->get('/properties/' . $property->id);

        $response->assertStatus(404);
    }
}
