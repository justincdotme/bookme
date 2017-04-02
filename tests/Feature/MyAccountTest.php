<?php

namespace Tests\Feature;

use App\Core\Phone;
use App\Core\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MyAccountTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function authenticated_user_can_access_their_account_page()
    {
        $this->user = factory(User::class)->create();

        $response = $this->actingAs($this->user)->get("/users/{$this->user->id}");

        $response->assertStatus(200);
        $response->assertViewHas('user');
        $response->assertSee($this->user->first_name);
        $response->assertSee($this->user->last_name);
        $response->assertSee($this->user->email);
    }

    /**
     * @test
     */
    public function unauthenticated_user_cannot_access_their_account_page()
    {
        $this->user = factory(User::class)->create();

        $response = $this->get("/users/{$this->user->id}");

        $response->assertRedirect('/login');
    }

    /**
     * @test
     */
    public function authenticated_user_cannot_access_another_users_account_page()
    {
        $this->user = factory(User::class)->create([
            'id' => 1
        ]);
        $user2 = factory(User::class)->create([
            'id' => 2
        ]);

        $response = $this->actingAs($this->user)->get("/users/{$user2->id}");

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function first_name_is_required_to_update_standard_user()
    {
        $this->user = factory(User::class)->create();
        $this->response = $this->actingAs($this->user)->put("/users/{$this->user->id}", [
            'last_name' => 'bar',
            'phone' => 1231231231
        ]);

        $this->assertFieldHasValidationError('first_name');
    }

    /**
     * @test
     */
    public function last_name_is_required_to_update_standard_user()
    {
        $this->user = factory(User::class)->create();
        $this->response = $this->actingAs($this->user)->put("/users/{$this->user->id}", [
            'first_name' => 'foo',
            'phone' => 1231231231
        ]);

        $this->assertFieldHasValidationError('last_name');
    }

    /**
     * @test
     */
    public function phone_number_is_required_to_update_standard_user()
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
    public function phone_number_must_be_numeric_to_update_standard_user()
    {
        $this->user = factory(User::class)->create();
        $this->response = $this->actingAs($this->user)->put("/users/{$this->user->id}", [
            'first_name' => 'foo',
            'last_name' => 'bar',
            'phone' => "abc123"
        ]);

        $this->assertFieldHasValidationError('phone');
    }

    /**
     * @test
     */
    public function authenticated_users_can_update_their_account_info()
    {
        $this->user = factory(User::class)->create([
            'first_name' => 'test',
            'last_name' => 'user'
        ]);
        factory(Phone::class)->create([
            'user_id' => 1
        ]);

        $response = $this->actingAs($this->user)->put("/users/{$this->user->id}", [
            'first_name' => 'foo',
            'last_name' => 'bar',
            'phone' => 9999999999
        ]);

        $response->assertStatus(200);
        $responseUser = $response->decodeResponseJson()['user'];
        $this->assertEquals('foo', $responseUser['first_name']);
        $this->assertEquals('bar', $responseUser['last_name']);
        $this->assertEquals(9999999999, $responseUser['phones'][0]['phone']);
    }
}
