<?php

namespace Tests\Browser;

use App\Core\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginTest extends DuskTestCase
{
    protected $user;

    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create([
            'password' => bcrypt('secret')
        ]);
    }

    /**
     * @test
     */
    public function a_non_authenticated_user_can_log_in()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertSee('Login')
                    ->type('email', $this->user->email)
                    ->type('password', 'secret')
                    ->press('Login')
                    ->assertPathIs('/');
        });
    }

    /**
     * @test
     */
    public function an_authenticated_user_cant_visit_login()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/login')
                ->assertPathIs('/');
        });
    }

    protected function tearDown()
    {
        parent::tearDown();
    }
}
