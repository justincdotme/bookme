<?php

namespace Tests\Unit;

use App\Core\Property;
use App\Core\State;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PropertyTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * @test
     */
    public function it_can_get_formatted_rate()
    {
        $property = factory(Property::class)->make([
            'rate' => 123.45
        ]);

        $this->assertEquals('$123.45',$property->formatted_rate);
    }

    /**
     * @test
     */
    public function it_can_get_formatted_address()
    {
        $property = factory(Property::class)->make([
            'street_address_line_1' => '1234 Any St',
            'street_address_line_2' => 'Apt. B',
            'city' => 'Vancouver',
            'zip' => 12345
        ]);

        $state = factory(State::class)->make([
            'abbreviation' => 'WA'
        ]);

        $property->setRelation('state', $state);

        $this->assertEquals("1234 Any St\nApt. B\nVancouver, WA 12345", $property->formatted_address);
    }

    /**
     * @test
     */
    public function properties_with_available_status_can_be_accessed()
    {
        $availableProperty = factory(Property::class)->states(['available'])->create();
        $unavailableProperty = factory(Property::class)->states(['unavailable'])->create();

        $availableProperties = Property::available()->get();

        $this->assertTrue($availableProperties->contains($availableProperty));
        $this->assertFalse($availableProperties->contains($unavailableProperty));
    }
}
