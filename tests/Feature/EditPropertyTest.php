<?php

namespace Tests\Feature;

use App\Core\Property\Property;
use App\Core\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EditPropertyTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function admin_users_can_edit_properties()
    {
        $this->user = factory(User::class)->states(['admin'])->create();

        $response = $this->editProperty([], true);

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function standard_users_cannot_update_properties()
    {
        $this->user = factory(User::class)->states(['standard'])->make();

        $response = $this->editProperty([], true);

        $response->assertStatus(403);
    }
    /**
     * @test
     */
    public function property_name_is_required_to_create_property()
    {
        $values = factory(Property::class)->states(['available'])->make()->toArray();
        unset($values['name']);

        $this->response = $this->editProperty($values, false);

        $this->assertFieldHasValidationError('name');
    }

    /**
     * @test
     */
    public function status_is_required_to_create_property()
    {
        $values = factory(Property::class)->states(['available'])->make()->toArray();
        unset($values['status']);

        $this->response = $this->editProperty($values, false);

        $this->assertFieldHasValidationError('status');
    }

    /**
     * @test
     */
    public function street_address_is_required_to_create_property()
    {
        $values = factory(Property::class)->states(['available'])->make()->toArray();
        unset($values['street_address_line_1']);

        $this->response = $this->editProperty($values, false);

        $this->assertFieldHasValidationError('street_address_line_1');
    }

    /**
     * @test
     */
    public function city_is_required_to_create_property()
    {
        $values = factory(Property::class)->states(['available'])->make()->toArray();
        unset($values['city']);

        $this->response = $this->editProperty($values, false);

        $this->assertFieldHasValidationError('city');
    }

    /**
     * @test
     */
    public function state_id_is_required_to_create_property()
    {
        $values = factory(Property::class)->states(['available'])->make()->toArray();
        unset($values['state_id']);

        $this->response = $this->editProperty($values, false);

        $this->assertFieldHasValidationError('state_id');
    }

    /**
     * @test
     */
    public function state_id_must_be_an_integer()
    {
        $values = factory(Property::class)->states(['available'])->make()->toArray();
        $values['state_id'] = 'WA';

        $this->response = $this->editProperty($values, false);

        $this->assertFieldHasValidationError('state_id');
    }

    /**
     * @test
     */
    public function zip_code_is_required()
    {
        $values = factory(Property::class)->states(['available'])->make()->toArray();
        unset($values['zip']);

        $this->response = $this->editProperty($values, false);

        $this->assertFieldHasValidationError('zip');
    }

    /**
     * @test
     */
    public function zip_code_must_be_an_integer()
    {
        $values = factory(Property::class)->states(['available'])->make()->toArray();
        $values['zip'] = 'code';

        $this->response = $this->editProperty($values, false);

        $this->assertFieldHasValidationError('zip');
    }

    /**
     * @test
     */
    public function it_edits_an_existing_property()
    {
        $this->property = factory(Property::class)->create([
            'name' => 'Beach House',
            'rate' => 12345,
            'short_description' => 'Test short description',
            'long_description' => 'This is a test long description',
            'street_address_line_1' => '1234 Any St',
            'street_address_line_2' => 'Apt. B',
            'city' => 'Vancouver',
            'state_id' => 1,
            'zip' => 12345
        ]);

        $data = $this->property->toArray();
        $data['name'] = 'Foo House';
        $data['rate'] = 9999;
        $response = $this->editProperty($data, false);

        $modifiedProperty = Property::find($this->property->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => 'success'
        ]);
        $this->assertNotEquals($modifiedProperty->name, $this->property->name);
        $this->assertNotEquals($modifiedProperty->rate, $this->property->rate);
    }

    /**
     * @test
     */
    public function cannot_update_property_that_doesnt_exist()
    {
        $this->user = factory(User::class)->states(['admin'])->create();

        $response = $this->actingAs($this->user)->json('PUT', "/properties/3", []);

        $response->assertStatus(422);
    }
}
