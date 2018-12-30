<?php

namespace Tests\Feature;

use App\Core\User;
use App\Mail\UserRegistrationConfirmation;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RegisterUsersTest extends TestCase
{
    use DatabaseMigrations;

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
        $response = $this->registerUser([
            'phone' => 1231231231,
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
            'phone' => 1231231231,
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
            'phone' => 1231231231,
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
            'phone' => 1231231231,
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
            'phone' => 1231231231,
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
            'phone' => 1231231231,
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
    public function phone_number_is_required_to_register_standard_user()
    {
        $this->response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test.user@justinc.me',
            'password' => 'abc123',
            'password_confirmation' => 'abc123'
        ]);

        $this->assertFieldHasValidationError('phone');
    }

    /**
     * @test
     */
    public function phone_number_must_be_numeric_to_register_standard_user()
    {
        $this->response = $this->post('/register', [
            'phone' => "abc123cba",
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test.user@justinc.me',
            'password' => 'abc123',
            'password_confirmation' => 'abc123'
        ]);

        $this->assertFieldHasValidationError('phone');
    }

    /**
     * @test
     */
    public function it_sends_welcome_email_to_new_users()
    {
        //TODO - Queue the email
        Mail::fake();
        $this->registerUser([
            'phone' => 1231231231,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test.user@justinc.me',
            'password' => 'abc123',
            'password_confirmation' => 'abc123'
        ]);

        Mail::assertSent(UserRegistrationConfirmation::class, function ($mail) {
            return $mail->hasTo('test.user@justinc.me');
        });
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