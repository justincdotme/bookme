<?php

namespace Tests\Feature;

use App\Core\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PasswordResetTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function unauthenticated_users_can_view_password_reset_request_form()
    {
        $response = $this->get('password/reset');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function unauthenticated_user_can_request_password_reset()
    {
        Notification::fake();
        $user = factory(User::class)->create();

        $response = $this->post('password/email', [
            'email' => $user->email
        ]);

        $response->assertStatus(302);
        $this->assertCount(
            1,
            DB::select("SELECT * FROM password_resets WHERE email = :email", ['email' => $user->email])
        );
        Notification::assertSentTo($user, ResetPassword::class);
    }

    /**
     * @test
     */
    public function email_address_is_required_to_request_password_reset()
    {
        $this->response = $this->post('password/email', []);

        $this->assertFieldHasValidationError('email');
    }

    /**
     * @test
     */
    public function unauthenticated_user_can_view_password_reset_form()
    {
        $response = $this->get('password/reset/12345abcde12345abcde');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function email_address_is_required_to_reset_password()
    {
        $this->response = $this->post('password/reset', [
            'token' => '12345abcde12345abcde',
            'password' => 'abc123'
        ]);

        $this->assertFieldHasValidationError('email');
    }

    /**
     * @test
     */
    public function token_is_required_to_reset_password()
    {
        $this->response = $this->post('password/reset', [
            'email' => 'foo@bar.com',
            'password' => 'abc123'
        ]);

        $this->assertFieldHasValidationError('token');
    }

    /**
     * @test
     */
    public function password_is_required_to_reset_password()
    {
        $this->response = $this->post('password/reset', [
            'email' => 'foo@bar.com',
            'token' => '12345abcde12345abcde',
        ]);

        $this->assertFieldHasValidationError('password');
    }
}
