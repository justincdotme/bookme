<?php

namespace Tests\Feature;

use App\Core\Property;
use App\Core\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AddPropertyTest extends TestCase
{
    use DatabaseMigrations;

    protected $property;

    /**
     * @test
     */
    public function admin_users_can_add_properties()
    {
        $this->user = factory(User::class)->states(['admin'])->create();

        $response = $this->addProperty();

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function standard_users_cannot_add_properties()
    {
        $this->user = factory(User::class)->states(['standard'])->make();

        $response = $this->addProperty();

        $response->assertStatus(403);
    }

    /**
     * Utility method to add a property.
     *
     * @param array $params
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function addProperty(array $params = null)
    {
        if (null === $this->property) {
            $this->property = factory(Property::class)->states(['available'])->create();
        }

        if (null === $this->user) {
            $this->user = factory(User::class)->states(['admin'])->create();
        }

        if (null == $params) {
            $params = $this->property->toArray();
        }

        $this->be($this->user);

        return $this->json('POST', "/properties", $params);
    }
}
