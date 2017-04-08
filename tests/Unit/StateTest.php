<?php

namespace Tests\Unit;

use App\Core\State;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StateTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function it_can_generate_list_of_states()
    {
        $state = new State();
        $this->artisan('db:seed');

        $list = $state->getList();

        $this->assertCount(51, $list);
    }
}
