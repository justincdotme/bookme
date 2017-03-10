<?php

namespace Tests\Feature;

use App\Core\Property;
use App\Core\Reservation;
use App\Core\User;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReservePropertyTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function it_returns_error_when_property_is_already_reserved()
    {
        $property = factory(Property::class)->states(['available'])->create();
        factory(Reservation::class)->create([
            'date_start' => Carbon::parse('+1 week')->toDateString(),
            'date_end' => Carbon::parse('+10 days')->toDateString()
        ]);
        $user = factory(User::class)->states(['standard'])->create();
        $this->be($user);

        $response = $this->reserveProperty([
            'date_start' => Carbon::parse('+1 week')->toDateString(),
            'date_end' => Carbon::parse('+10 days')->toDateString()
        ], $property);

        $response->assertJsonFragment([
            'status' => 'error',
            'msg' => 'The property is unavailable for this date range.'
        ]);
    }

    /**
     * @test
     */
    public function it_returns_success_when_property_is_not_already_reserved()
    {
        $property = factory(Property::class)->states(['available'])->create();
        factory(Reservation::class)->create([
            'date_start' => Carbon::parse('+2 days')->toDateString(),
            'date_end' => Carbon::parse('+4 days')->toDateString()
        ]);
        $user = factory(User::class)->states(['standard'])->create();
        $this->be($user);

        $response = $this->reserveProperty([
            'date_start' => Carbon::parse('+1 week')->toDateString(),
            'date_end' => Carbon::parse('+10 days')->toDateString()
        ], $property);

        $response->assertJsonFragment([
            'status' => 'success'
        ]);
    }

    /**
     * @test
     */
    public function it_returns_not_found_exception_for_missing_or_invalid_property_id()
    {
        $this->disableExceptionHandling();
        $params = [
            'date_start' => Carbon::parse('+1 week'),
            'date_end' => Carbon::parse('+10 days')
        ];

        try {
            $this->json('POST', "/properties//reservations", $params);
        } catch (NotFoundHttpException $e) {
            return;
        }

        $this->fail('Request succeeded without property id.');
    }

    /**
     * @test
     */
    public function date_start_is_required_to_reserve_a_property()
    {
        $response = $this->reserveProperty([
            'date_end' => Carbon::now()->toDateTimeString()
        ]);

        $this->assertFieldHasValidationError('date_start', $response);
    }

    /**
     * @test
     */
    public function date_end_is_required_to_reserve_a_property()
    {
        $response = $this->reserveProperty([
            'date_start' => Carbon::now()->toDateTimeString()
        ]);

        $this->assertFieldHasValidationError('date_end', $response);
    }

    protected function reserveProperty(array $params, $property = null)
    {
        if (null === $property) {
            $property = factory(Property::class)->states(['available'])->create();
        }
        $user = factory(User::class)->states(['standard'])->create();
        $this->be($user);

        return $this->json('POST', "/properties/{$property->id}/reservations", $params);
    }
}
