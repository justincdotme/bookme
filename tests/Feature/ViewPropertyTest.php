<?php

namespace Tests\Feature;

use App\Core\Property\Property;
use App\Core\Property\PropertyImage;
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
        $this->makeTestProperty();

        $response = $this->get('/properties/' . $this->property->id);

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
        $property = factory(Property::class)->states(['unavailable'])->create();

        $response = $this->get('/properties/' . $property->id);

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function property_images_are_included_in_html_on_initial_load_if_property_has_images()
    {
        $this->makeTestProperty()->addImages(1);

        $response = $this->get('/properties/' . $this->property->id);

        $response->assertStatus(200);
        $response->assertViewHas('images');
        $response->assertSee('<div id="image-container">');
        $response->assertDontSee('<span>There are no images for this property.</span>');
    }

    /**
     * @test
     */
    public function alternate_message_is_shown_if_property_has_no_images()
    {
        $this->makeTestProperty();

        $response = $this->get('/properties/' . $this->property->id);

        $response->assertStatus(200);
        $response->assertViewHas('images');
        $response->assertSee('<span>There are no images for this property.</span>');
    }

    protected function makeTestProperty()
    {
        $this->property = factory(Property::class)->make([
            'name' => 'Beach House',
            'rate' => 12345,
            'short_description' => 'Test short description',
            'long_description' => 'This is a test long description',
            'street_address_line_1' => '1234 Any St',
            'street_address_line_2' => 'Apt. B',
            'city' => 'Vancouver',
            'zip' => 12345
        ]);

        $this->state = factory(State::class)->create([
            'abbreviation' => 'WA'
        ]);

        $this->property->state()->associate($this->state);
        $this->property->save();

        return $this;
    }

    /**
     * @param $count
     * @return $this
     */
    protected function addImages($count)
    {
        if ($count) {
            factory(PropertyImage::class, $count)->create([
                'property_id' => $this->property->id
            ]);
        }

        return $this;
    }
}
