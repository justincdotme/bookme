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
        factory(State::class)->create([
            'abbreviation' => 'WA'
        ]);
        factory(State::class)->create([
            'abbreviation' => 'OR'
        ]);

        $list = (new State())->getList();

        $this->assertCount(2, $list);
    }
}
