<?php

namespace Tests\Unit;

use App\Core\Property\Property;
use App\Core\Property\PropertySearch;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PropertySearchTest extends TestCase
{
    use DatabaseMigrations;

    protected $properties;
    protected $propertySearch;

    public function setUp()
    {
        parent::setUp();

        $this->properties = collect([]);
        $this->properties->push(factory(Property::class, 2)->create([
            'city' => 'vancouver',
            'state_id' => 1
        ]));
        $this->properties->push(factory(Property::class, 2)->create([
            'city' => 'portland',
            'state_id' => 2
        ]));

        $this->propertySearch = new PropertySearch(new Property());
    }

    /**
     * @test
     */
    public function search_method_returns_instance_of_property_search()
    {
        $search = $this->propertySearch->search();

        $this->assertInstanceOf(PropertySearch::class, $search);
    }

    /**
     * @test
     */
    public function it_filters_properties_by_city_and_state()
    {
        $search = $this->propertySearch
            ->setType('city-state')
            ->search(['city' => 'Vancouver', 'state' => 1])
            ->getResults();

        $this->assertCount(2, $search);
    }

    /**
     * @test
     */
    public function it_can_paginate_search_results()
    {
        $search = $this->propertySearch
            ->setType('city-state')
            ->search(['city' => 'Vancouver', 'state' => 1])
            ->getResults(1);

        $this->assertCount(1, $search);
    }

    /**
     * @test
     */
    public function it_returns_all_properties_if_no_type_and_query_supplied()
    {
        $search = $this->propertySearch
            ->search()
            ->getResults();

        $this->assertCount(4, $search);
    }
}
