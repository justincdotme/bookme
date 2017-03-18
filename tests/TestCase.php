<?php

namespace Tests;

use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $response;

    /**
     * Utility method to disable exception handling.
     * Useful when we need to see a stack trace.
     */
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

    /**
     * Helper method to test that a specific field has a validation error.
     *
     * @param $field
     */
    protected function assertFieldHasValidationError($field)
    {
        $this->response->assertStatus(422);
        $this->assertArrayHasKey($field, $this->response->decodeResponseJson());
    }
}
