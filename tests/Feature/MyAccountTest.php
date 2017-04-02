<?php

namespace Tests\Feature;

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
}
