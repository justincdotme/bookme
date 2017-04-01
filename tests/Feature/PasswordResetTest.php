<?php

namespace Tests\Feature;

use App\Core\User;
use EmailTestHelpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use TestingMailEventListener;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PasswordResetTest extends TestCase
{
    use DatabaseMigrations;
    use EmailTestHelpers;

    protected function setUp()
    {
        parent::setUp();
        Mail::getSwiftMailer()
            ->registerPlugin(new TestingMailEventListener($this));
    }

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
        $user = factory(User::class)->create();

        $response = $this->post('password/email', [
            'email' => $user->email
        ]);

        $response->assertStatus(302);
        $this->assertCount(
            1,
            DB::select("SELECT * FROM password_resets WHERE email = :email", ['email' => $user->email])
        );
        $this->seeEmailWasSent();
        $this->seeEmailsSent(1);
        $this->seeEmailTo($user->email);
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
