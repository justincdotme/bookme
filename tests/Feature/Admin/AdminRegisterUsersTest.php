<?php

namespace Tests\Feature\Admin;

use App\Core\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminRegisterUsersTest extends TestCase
{
    use DatabaseMigrations;

    protected $standardUser;

    protected function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->states(['admin'])->create();
        $this->standardUser = factory(User::class)->states(['standard'])->create();
    }

    /**
     * @test
     */
    public function admin_users_can_create_new_admin_users()
    {
        $response = $this->actingAs($this->user)->post("/admin/users", [
            'email' => 'test.user@justinc.me',
            'password' => 123123,
            'password_confirmation' => 123123,
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => 'success'
        ]);
        $json = $response->decodeResponseJson();
        $this->assertArrayHasKey('user', $json);
    }

    /**
     * @test
     */
    public function standard_users_cannot_create_new_admin_users()
    {
        $response = $this->actingAs($this->standardUser)->post("/admin/users", [
            'email' => 'test.user@justinc.me',
            'password' => 123123,
            'password_confirmation' => 123123,
        ]);

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function email_is_required_to_register_admin_user()
    {
        $this->response = $this->actingAs($this->user)->post('/admin/users', [
            'password' => 123123,
            'password_confirmation' => 123123,
        ]);

        $this->assertFieldHasValidationError('email');
    }

    /**
     * @test
     */
    public function password_is_required_to_register_admin_user()
    {
        $this->response = $this->actingAs($this->user)->post('/admin/users', [
            'email' => 'test.user@justinc.me',
            'password_confirmation' => 123123
        ]);

        $this->assertFieldHasValidationError('password');
    }

    /**
     * @test
     */
    public function password_confirmation_is_required_to_register_admin_user()
    {
        $this->response = $this->actingAs($this->user)->post('/admin/users', [
            'email' => 'test.user@justinc.me',
            'password' => 123123
        ]);

        $this->assertFieldHasValidationError('password');
    }
}