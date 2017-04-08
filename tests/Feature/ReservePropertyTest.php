<?php

namespace Tests\Feature;

use App\Core\Payment\PaymentGatewayInterface;
use App\Core\Payment\TestPaymentGateway;
use App\Core\Property\Property;
use App\Core\Reservation;
use App\Core\State;
use App\Core\User;
use Carbon\Carbon;
use EmailTestHelpers;
use Illuminate\Support\Facades\Mail;
use TestingMailEventListener;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReservePropertyTest extends TestCase
{
    use DatabaseMigrations;
    use EmailTestHelpers;

    protected $paymentGateway;
    protected $property;
    protected $user;

    public function setUp($name = null, array $data = [], $dataName = '')
    {
        parent::setUp($name, $data, $dataName);
        $this->app->instance(PaymentGatewayInterface::class, new TestPaymentGateway());
        $this->paymentGateway = app()->make(PaymentGatewayInterface::class);
        Mail::getSwiftMailer()
            ->registerPlugin(new TestingMailEventListener($this));
    }

    /**
     * @test
     */
    public function authenticated_user_can_view_reservation_create_view()
    {
        $this->user = factory(User::class)->states(['standard'])->create();
        $this->property = factory(Property::class)->create();

        $response = $this->actingAs($this->user)->get("/properties/{$this->property->id}/reservations/create");

        $response->assertStatus(200);
        $response->assertViewHas('stateList');
        $response->assertViewHas('property');
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

        $this->property = factory(Property::class)->make([
            'id' => 1,
            'rate' => 50000
        ]);
        $state = factory(State::class)->create([
            'abbreviation' => 'WA'
        ]);

        $this->property->state()->associate($state);
        $this->property->save();

        $this->response = $this->reserveProperty([
            'date_start' => Carbon::now()->toDateString(),
            'date_end' => Carbon::parse('+1 week')->toDateString(),
            'payment_token' => $this->paymentGateway->getValidTestToken(),
            'line1' => '123 Foo St',
            'city' => 'Fooville',
            'state_id' => 1,
            'zip' => 12345
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
            'payment_token' => $this->paymentGateway->getValidTestToken(),
            'line1' => '123 Foo St',
            'city' => 'Fooville',
            'state_id' => 1,
            'zip' => 12345
        ], $useUnauthenticatedUser);

        $this->response->assertStatus(401);
        $this->assertCount(0, $this->property->reservations()->get());
        $this->assertEquals(0, $this->paymentGateway->getTotalCharges());
    }

    /**
     * @test
     */
    public function it_sends_a_confirmation_email_for_successful_reservation()
    {
        $this->user = factory(User::class)->states(['standard'])->make([
            'id' => 1,
            'email' => 'foo@fighter.com'
        ]);
        $this->property = factory(Property::class)->make();
        $state = factory(State::class)->create([
            'abbreviation' => 'WA'
        ]);
        $this->property->state()->associate($state);
        $this->property->save();
        $dateStart = Carbon::now();
        $dateEnd = Carbon::parse('+1 week');

        $this->response = $this->reserveProperty([
            'date_start' => $dateStart->toDateString(),
            'date_end' => $dateEnd->toDateString(),
            'payment_token' => $this->paymentGateway->getValidTestToken(),
            'line1' => '123 Foo St',
            'city' => 'Fooville',
            'state_id' => 1,
            'zip' => 12345
        ]);

        $userName = e($this->user->name);
        $this->seeEmailWasSent();
        $this->seeEmailsSent(1);
        $this->seeEmailTo($this->user->email);
        $this->seeEmailFrom('no-reply@bookme.justinc.me');
        $this->seeEmailContains("Thank you for your reservation, {$userName}");
        $this->seeEmailContains("Check In: {$dateStart->toFormattedDateString()}");
        $this->seeEmailContains("Check Out: {$dateEnd->toFormattedDateString()}");
        $this->seeEmailContains("Length of Stay: 7 days.");
        $this->seeEmailContains("Reservation Number: 1");
        $this->seeEmailContains("Name: {$this->property->name}");
        $this->seeEmailContains("Address: " . nl2br($this->property->formatted_address));
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
            'payment_token' => $this->paymentGateway->getValidTestToken(),
            'line1' => '123 Foo St',
            'city' => 'Fooville',
            'state_id' => 1,
            'zip' => 12345
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
        $this->property = factory(Property::class)->make();
        $state = factory(State::class)->create([
            'abbreviation' => 'WA'
        ]);
        $this->property->state()->associate($state);
        $this->property->save();

        $this->response = $this->reserveProperty([
            'date_start' => Carbon::parse('+1 week')->toDateString(),
            'date_end' => Carbon::parse('+10 days')->toDateString(),
            'payment_token' => $this->paymentGateway->getValidTestToken(),
            'line1' => '123 Foo St',
            'city' => 'Fooville',
            'state_id' => 1,
            'zip' => 12345
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
        $this->user = factory(User::class)->states(['standard'])->create([
            'id' => 1
        ]);
        $params = [
            'date_start' => Carbon::parse('+1 week'),
            'date_end' => Carbon::parse('+10 days'),
            'line1' => '123 Foo St',
            'city' => 'Fooville',
            'state_id' => 1,
            'zip' => 12345
        ];

        $response = $this->actingAs($this->user)->json('POST', "/properties/x/reservations", $params);

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
            'payment_token' => 'invalid-payment-token',
            'line1' => '123 Foo St',
            'city' => 'Fooville',
            'state_id' => 1,
            'zip' => 12345
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
     * @test
     */
    public function street_address_is_required_to_reserve_a_property()
    {
        $this->response = $this->reserveProperty([]);

        $this->assertFieldHasValidationError('line1');
    }

    /**
     * @test
     */
    public function city_is_required_to_reserve_a_property()
    {
        $this->response = $this->reserveProperty([]);

        $this->assertFieldHasValidationError('city');
    }

    /**
     * @test
     */
    public function state_id_is_required_to_reserve_a_property()
    {
        $this->response = $this->reserveProperty([]);

        $this->assertFieldHasValidationError('state_id');
    }

    /**
     * @test
     */
    public function state_id_must_be_integer_to_reserve_a_property()
    {
        $this->response = $this->reserveProperty([
            'state_id' => 'x'
        ]);

        $this->assertFieldHasValidationError('state_id');
    }

    /**
     * @test
     */
    public function zip_is_required_to_reserve_a_property()
    {
        $this->response = $this->reserveProperty([]);

        $this->assertFieldHasValidationError('zip');
    }

    /**
     * @test
     */
    public function zip_must_be_numeric_to_reserve_a_property()
    {
        $this->response = $this->reserveProperty([
            'zip' => 'abc'
        ]);

        $this->assertFieldHasValidationError('zip');
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
            $this->actingAs($this->user);
        }

        return $this->json('POST', "/properties/{$this->property->id}/reservations", $params);
    }
}