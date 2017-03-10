<?php

namespace Tests;

use App\Core\Property;
use App\Core\User;
use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function disableExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct() {}
            public function report(\Exception $e) {}
            public function render($request, \Exception $e) {
                throw $e;
            }
        });
    }

    protected function assertFieldHasValidationError($field, $response)
    {
        $response->assertStatus(422);
        $this->assertArrayHasKey($field, $response->decodeResponseJson());
    }

    protected function checkValidation(array $params)
    {
        $property = factory(Property::class)->states(['available'])->create();
        $user = factory(User::class)->states(['standard'])->make();
        $this->be($user);

        return $this->json('POST', "/properties/{$property->id}/reservations/check", $params);
    }
}
