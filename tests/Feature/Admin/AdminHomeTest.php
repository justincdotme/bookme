<?php

namespace Tests\Feature\Admin;

use App\Core\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminHomeTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function admin_users_can_view_admin_homepage()
    {
        $this->user = factory(User::class)->states(['admin'])->create();

        $response = $this->actingAs($this->user)->get('/admin');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function standard_users_can_view_admin_homepage()
    {
        $this->user = factory(User::class)->states(['standard'])->create();

        $response = $this->actingAs($this->user)->get('/admin');

        $response->assertStatus(403);
    }
}
