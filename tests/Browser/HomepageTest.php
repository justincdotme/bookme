<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class HomepageTest extends DuskTestCase
{
    //TODO - Consider migrating in DuskTestCase
    use DatabaseMigrations;

    /**
     * @test
     */
    public function navigationIsPresent()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertTitleContains('Home')
                ->assertSee('Search')
                ->assertSee('Contact')
                ->assertSee('Login')
                ->assertSee('Register')
                ->assertSeeIn('.active', 'Home');
        });
    }
}
