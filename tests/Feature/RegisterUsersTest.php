<?php

namespace Tests\Feature;

use App\Core\User;
use EmailTestHelpers;
use Illuminate\Support\Facades\Mail;
use TestingMailEventListener;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RegisterUsersTest extends TestCase
{
    use DatabaseMigrations;
    use EmailTestHelpers;


    public function setUp($name = null, array $data = [], $dataName = '')
    {
        parent::setUp($name, $data, $dataName);
        Mail::getSwiftMailer()
            ->registerPlugin(new TestingMailEventListener($this));
    }

    /**
     * @test
     */
    public function guest_user_can_access_signup_form()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function authenticated_users_cannot_access_signup_form()
    {
        $user = factory(User::class)->states(['standard'])->create();

        $response = $this->actingAs($user)->get('/register');

        $response->assertStatus(302);
    }

    /**
     * @test
     */
    public function registered_users_cannot_create_registered_users()
    {
        $user = factory(User::class)->states(['standard'])->create();

        $response = $this->actingAs($user)->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test.user@justinc.me',
            'password' => 'abc123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    /**
     * @test
     */
    public function guest_users_can_create_registered_users()
    {
        $this->disableExceptionHandling();
        $response = $this->registerUser([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test.user@justinc.me',
            'password' => 'abc123',
            'password_confirmation' => 'abc123'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * @test
     */
    public function first_name_is_required_to_register_standard_user()
    {
        $this->response = $this->post('/register', [
            'last_name' => 'User',
            'email' => 'test.user@justinc.me',
            'password' => 'abc123',
        ]);

        $this->assertFieldHasValidationError('first_name');
    }

    /**
     * @test
     */
    public function last_name_is_required_to_register_standard_user()
    {
        $this->response = $this->post('/register', [
            'first_name' => 'Test',
            'email' => 'test.user@justinc.me',
            'password' => 'abc123',
        ]);

        $this->assertFieldHasValidationError('last_name');
    }

    /**
     * @test
     */
    public function email_is_required_to_register_standard_user()
    {
        $this->response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'password' => 'abc123',
        ]);

        $this->assertFieldHasValidationError('email');
    }

    /**
     * @test
     */
    public function password_is_required_to_register_standard_user()
    {
        $this->response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test.user@justinc.me'
        ]);

        $this->assertFieldHasValidationError('password');
    }

    /**
     * @test
     */
    public function password_confirmation_is_required_to_register_standard_user()
    {
        $this->response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test.user@justinc.me',
            'password' => 'abc123'
        ]);

        $this->assertFieldHasValidationError('password');
    }

    /**
     * @test
     */
    public function it_sends_welcome_email_to_new_users()
    {
        $this->registerUser([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test.user@justinc.me',
            'password' => 'abc123',
            'password_confirmation' => 'abc123'
        ]);

        $this->seeEmailWasSent();
        $this->seeEmailsSent(1);
        $this->seeEmailTo('test.user@justinc.me');
        $this->seeEmailFrom('no-reply@bookme.justinc.me');
        $this->seeEmailContains("Welcome, Test User");
    }

    /**
     * Helper method to register a new user.
     *
     * @param $params
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function registerUser($params)
    {
        return $this->post('/register', $params);
    }
}