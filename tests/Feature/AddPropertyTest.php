<?php

namespace Tests\Feature;

use App\Core\Property;
use App\Core\State;
use App\Core\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AddPropertyTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function admin_users_can_add_properties()
    {
        $this->user = factory(User::class)->states(['admin'])->create();

        $response = $this->createProperty([], true);

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function standard_users_cannot_add_properties()
    {
        $this->user = factory(User::class)->states(['standard'])->make();

        $response = $this->createProperty([], true);

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function property_name_is_required_to_create_property()
    {
        $values = factory(Property::class)->states(['available'])->make()->toArray();
        unset($values['name']);

        $this->response = $this->createProperty($values, false);

        $this->assertFieldHasValidationError('name');
    }

    /**
     * @test
     */
    public function status_is_required_to_create_property()
    {
        $values = factory(Property::class)->states(['available'])->make()->toArray();
        unset($values['status']);

        $this->response = $this->createProperty($values, false);

        $this->assertFieldHasValidationError('status');
    }

    /**
     * @test
     */
    public function street_address_is_required_to_create_property()
    {
        $values = factory(Property::class)->states(['available'])->make()->toArray();
        unset($values['street_address_line_1']);

        $this->response = $this->createProperty($values, false);

        $this->assertFieldHasValidationError('street_address_line_1');
    }

    /**
     * @test
     */
    public function city_is_required_to_create_property()
    {
        $values = factory(Property::class)->states(['available'])->make()->toArray();
        unset($values['city']);

        $this->response = $this->createProperty($values, false);

        $this->assertFieldHasValidationError('city');
    }

    /**
     * @test
     */
    public function state_id_is_required_to_create_property()
    {
        $values = factory(Property::class)->states(['available'])->make()->toArray();
        unset($values['state_id']);

        $this->response = $this->createProperty($values, false);

        $this->assertFieldHasValidationError('state_id');
    }

    /**
     * @test
     */
    public function state_id_must_be_an_integer()
    {
        $values = factory(Property::class)->states(['available'])->make()->toArray();
        $values['state_id'] = 'WA';

        $this->response = $this->createProperty($values, false);

        $this->assertFieldHasValidationError('state_id');
    }

    /**
     * @test
     */
    public function if_rate_is_supplied_rate_must_be_an_integer()
    {
        $values = factory(Property::class)->states(['available'])->make([
            'rate' => 123.45
        ])->toArray();

        $this->response = $this->createProperty($values, false);

        $this->assertFieldHasValidationError('rate');
    }

    /**
     * @test
     */
    public function rate_is_not_required()
    {
        $values = factory(Property::class)->states(['available'])->make()->toArray();
        unset($values['rate']);

        $response = $this->createProperty($values, false);

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function it_creates_a_new_property()
    {
        $notFoundResponse = $this->json('GET', "/properties/1");
        $notFoundResponse->assertStatus(404);

        $state = factory(State::class)->create();
        $property = factory(Property::class)->states(['available'])->make([
            'state_id' => $state->id
        ]);
        $user = factory(User::class)->states(['admin'])->create();

        $params = $property->toArray();

        $this->be($user);
        $this->json('POST', "/properties", $params);

        $newPropertyResponse = $this->json('GET', "/properties/1");

        $newPropertyResponse->assertStatus(200);
        $newPropertyResponse->assertSee($params['name']);
        $newPropertyResponse->assertSee($property->getFormattedRateAttribute()); //assertSee requires string
        $newPropertyResponse->assertSee($params['short_description']);
        $newPropertyResponse->assertSee($params['long_description']);
        $newPropertyResponse->assertSee($params['street_address_line_1']);
        $newPropertyResponse->assertSee($params['city'] . ', ' . $state->abbreviation . ' ' . $params['zip']);
    }
}
