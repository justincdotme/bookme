<?php

namespace Tests\Feature;

use App\Core\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserAuthTest extends TestCase
{
    /**
     * @test
     */
    public function it_redirects_authenticated_admin_users_to_admin_route()
    {
        $user = factory(User::class)->states(['admin'])->make();
        $this->be($user);
        $response = $this->get('/login');

        $response->assertStatus(302);
        $response->assertRedirect('/admin');
    }

    /**
     * @test
     */
    public function it_redirects_authenticated_standard_users_to_home_route()
    {
        $user = factory(User::class)->states(['standard'])->make();
        $this->be($user);
        $response = $this->get('/login');

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }
}
