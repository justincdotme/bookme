<?php

namespace Tests\Browser;

use App\Core\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginTest extends DuskTestCase
{
    /**
     * @test
     * @return void
     */
    public function a_user_can_log_in()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt('secret')
        ]);
        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                ->assertSee('Login')
                    ->type('email', $user->email)
                    ->type('password', 'secret')
                    ->press('Login')
                    ->assertPathIs('/');
        });

        $user->delete();
    }
}
