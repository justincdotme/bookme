<?php

namespace Tests\Feature;

use App\Core\Billing\PaymentGatewayInterface;
use App\Core\Billing\TestPaymentGateway;
use App\Core\Property\Property;
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

    protected $paymentGateway;
    protected $property;
    protected $user;

    public function setUp($name = null, array $data = [], $dataName = '')
    {
        parent::setUp($name, $data, $dataName);
        $this->app->instance(PaymentGatewayInterface::class, new TestPaymentGateway());
        $this->paymentGateway = app()->make(PaymentGatewayInterface::class);
    }

    /**
     * @test
     */
    public function authenticated_user_can_make_reservation()
    {
        $this->user = factory(User::class)->states(['standard'])->create([
            'id' => 1,
            'email' => 'foo@bar.com'
        ]);
        $this->property= factory(Property::class)->create([
            'id' => 1,
            'rate' => 50000
        ]);

        $this->response = $this->reserveProperty([
            'date_start' => Carbon::now()->toDateString(),
            'date_end' => Carbon::parse('+1 week')->toDateString(),
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $reservation = $this->property->reservations()->first();

        $this->response->assertStatus(201);
        $this->assertEquals(350000, $this->paymentGateway->getTotalCharges());
        $this->assertEquals(350000, $reservation->amount);
        $this->assertEquals('foo@bar.com', $reservation->user->email);
    }

    /**
     * @test
     */
    public function unauthenticated_user_cannot_make_reservation()
    {
        $useUnauthenticatedUser = true;
        $this->response = $this->reserveProperty([
            'date_start' => Carbon::now()->toDateString(),
            'date_end' => Carbon::parse('+1 week')->toDateString(),
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ], $useUnauthenticatedUser);

        $this->response->assertStatus(403);
        $this->assertCount(0, $this->property->reservations()->get());
        $this->assertEquals(0, $this->paymentGateway->getTotalCharges());
    }

    /**
     * @test
     */
    public function it_returns_error_when_property_is_already_reserved()
    {
        factory(Reservation::class)->create([
            'date_start' => Carbon::parse('+1 week')->toDateString(),
            'date_end' => Carbon::parse('+10 days')->toDateString()
        ]);

        $this->response = $this->reserveProperty([
            'date_start' => Carbon::parse('+1 week')->toDateString(),
            'date_end' => Carbon::parse('+10 days')->toDateString(),
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->response->assertJsonFragment([
            'status' => 'error',
            'msg' => 'The property is unavailable for this date range.'
        ]);
    }

    /**
     * @test
     */
    public function it_returns_success_when_property_is_not_already_reserved()
    {
        factory(Reservation::class)->create([
            'date_start' => Carbon::parse('+2 days')->toDateString(),
            'date_end' => Carbon::parse('+4 days')->toDateString()
        ]);

        $this->response = $this->reserveProperty([
            'date_start' => Carbon::parse('+1 week')->toDateString(),
            'date_end' => Carbon::parse('+10 days')->toDateString(),
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->response->assertJsonFragment([
            'status' => 'success'
        ]);
    }

    /**
     * @test
     */
    public function it_returns_not_found_exception_for_missing_or_invalid_property_id()
    {
        $params = [
            'date_start' => Carbon::parse('+1 week'),
            'date_end' => Carbon::parse('+10 days')
        ];

        $response = $this->json('POST', "/properties//reservations", $params);

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function reservation_is_not_active_if_payment_fails()
    {
        $this->user = factory(User::class)->states(['standard'])->create([
            'id' => 1
        ]);

        $this->response = $this->reserveProperty([
            'date_start' => Carbon::parse('+1 days')->toDateString(),
            'date_end' => Carbon::parse('+2 days')->toDateString(),
            'payment_token' => 'invalid-payment-token'
        ]);

        $this->response->assertStatus(422);
        $this->response->assertJsonFragment([
            'status' => 'error',
            'msg' => 'The payment has failed, please try a different card.'
        ]);

        $this->assertNull(Reservation::where('user_id', 1)->active()->first());
    }

    /**
     * @test
     */
    public function date_start_is_required_to_reserve_a_property()
    {
        $this->response = $this->reserveProperty([]);

        $this->assertFieldHasValidationError('date_start');
    }

    /**
     * @test
     */
    public function payment_token_is_required_to_reserve_a_property()
    {
        $this->response = $this->reserveProperty([]);

        $this->assertFieldHasValidationError('payment_token');
    }

    /**
     * @test
     */
    public function date_end_is_required_to_reserve_a_property()
    {
        $this->response = $this->reserveProperty([]);

        $this->assertFieldHasValidationError('date_end');
    }

    /**
     * Utility method to set up a property reservation.
     *
     * @param array $params
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function reserveProperty(array $params, $noAuthenticatedUser = false)
    {
        if (null === $this->property) {
            $this->property = factory(Property::class)->states(['available'])->create();
        }

        if (null === $this->user) {
            $this->user = factory(User::class)->states(['standard'])->create();
        }

        if (!$noAuthenticatedUser) {
            $this->be($this->user);
        }

        return $this->json('POST', "/properties/{$this->property->id}/reservations", $params);
    }
}
