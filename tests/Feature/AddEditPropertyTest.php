<?php

namespace Tests\Feature;

use App\Core\Property;
use App\Core\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AddEditPropertyTest extends TestCase
{
    use DatabaseMigrations;

    protected $property;
    protected $user;

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
        $this->response = $this->createProperty([], false);

        $this->assertFieldHasValidationError('name');
    }

    /**
     * Utility method to add a property.
     *
     * @param array $params
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function createProperty(array $params, $autoPolulate = true)
    {
        if (null === $this->property) {
            $this->property = factory(Property::class)->states(['available'])->create();
        }

        if (null === $this->user) {
            $this->user = factory(User::class)->states(['admin'])->create();
        }

        if ($autoPolulate) {
            $params = $this->property->toArray();
        }

        $this->be($this->user);
        return $this->json('POST', "/properties", $params);
    }
}
