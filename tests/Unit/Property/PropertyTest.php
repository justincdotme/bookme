<?php

namespace Tests\Unit;

use App\Core\Property\Property;
use App\Core\Property\PropertyImage;
use App\Core\Reservation;
use App\Core\State;
use App\Core\User;
use App\Exceptions\AlreadyReservedException;
use Carbon\Carbon;
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
            'rate' => 12345
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

        $availableProperties = Property::get();
        $unavailableProperties = Property::withoutGLobalScopes()->get();

        $this->assertTrue($availableProperties->contains($availableProperty));
        $this->assertFalse($availableProperties->contains($unavailableProperty));
        $this->assertTrue($unavailableProperties->contains($unavailableProperty));
    }

    /**
     * @test
     */
    public function it_checks_if_property_is_available_between_dates()
    {
        $property = factory(Property::class)->states(['available'])->create();
        factory(Reservation::class)->states(['pending'])->create([
           'property_id' => $property->id,
            'date_start' => Carbon::parse('+1 week'),
            'date_end' => Carbon::parse('+10 days')
        ]);
        factory(Reservation::class)->states(['pending'])->create([
            'property_id' => $property->id,
            'date_start' => Carbon::parse('+2 weeks'),
            'date_end' => Carbon::parse('+3 weeks')
        ]);

        $this->assertTrue($property->isAvailableBetween(Carbon::now(), Carbon::parse('+6 days')));
        $this->assertFalse($property->isAvailableBetween(Carbon::parse('+2 weeks'), Carbon::parse('+3 weeks')));
    }

    /**
     * @test
     */
    public function it_throws_already_reserved_exception()
    {
        $property = factory(Property::class)->states(['available'])->make([
            'id' => 1
        ]);
        $user = factory(User::class)->make();
        factory(Reservation::class)->create([
            'property_id' => $property->id,
            'date_start' => Carbon::parse('+1 week'),
            'date_end' => Carbon::parse('+10 days')
        ]);
        $dateStart = Carbon::parse('+1 week');
        $dateEnd = Carbon::parse('+10 days');

        try {
            $property->reserveFor($dateStart, $dateEnd, $user);
            $this->expectException(AlreadyReservedException::class);
        } catch (AlreadyReservedException $e) {
            return;
        }

        $this->fail('Reservation succeeded even though property was already booked');
    }

    /**
     * @test
     */
    public function reserveFor_returns_reservation()
    {
        $property = factory(Property::class)->states(['available'])->make([
            'id' => 1
        ]);
        $user = factory(User::class)->make([
            'id' => 1
        ]);

        $dateStart = Carbon::parse('+1 week');
        $dateEnd = Carbon::parse('+10 days');
        $reservation = $property->reserveFor($dateStart, $dateEnd, $user);

        $this->assertInstanceOf(Reservation::class, $reservation);
    }

    /**
     * @test
     */
    public function it_returns_featured_properties()
    {
        $property = factory(Property::class)->states(['available'])->create([
            'id' => 1
        ]);
        factory(Property::class)->states(['available'])->create([
            'id' => 2,
            'featured' => true
        ]);

        $featuredProperties = $property->featured()->get();

        $this->assertCount(1, $featuredProperties);
        $this->assertEquals(2, $featuredProperties->first()->id);
    }
}
