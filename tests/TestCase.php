<?php

namespace Tests;

use App\Core\Property\Property;
use App\Core\State;
use App\Core\User;
use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\File;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $response;
    protected $property;
    protected $user;
    protected $state;

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
        $jsonResponse= $this->response->decodeResponseJson();
        if (!array_key_exists('errors', $jsonResponse)) {
            $this->fail('There is no errors array');
        }
        $this->response->assertStatus(422);
        $this->assertArrayHasKey($field, $jsonResponse['errors']);
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

        if (null === $this->state) {
            $this->state = factory(State::class)->create();
        }

        if ($autoPolulate) {
            $params = $this->property->toArray();
            $params['state_id'] = $this->state->id;
        }

        $this->be($this->user);
        return $this->json('POST', "/admin/properties", $params);

    }

    protected function editProperty($params, $autoPolulate = true)
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
        return $this->json('PUT', "/admin/properties/{$this->property->id}", $params);
    }


    /**
     * Utility method to copy test images to public dir
     *
     * @return string
     */
    protected function copyTestImages($name = null)
    {
        $stubPath = __DIR__ . "/stubs/test-1.jpg";
        $tmpName = (null === $name) ? "{$this->property->id}-original.jpg" : "{$name}.jpg";
        $tmpDir = storage_path("app/public/images/properties/{$this->property->id}");
        $tmpPath = "{$tmpDir}/{$tmpName}";
        File::makeDirectory($tmpDir, 0775, true, true);
        File::copy($stubPath, $tmpPath);

        return $tmpPath;
    }
}
