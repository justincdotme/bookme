<?php

use App\Core\Property\Property;
use Illuminate\Database\Seeder;

class PropertiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Create 5 properties per state with random city names
        $stateCount = range(1, 51);
        foreach ($stateCount as $stateId) {
            for ($pi=1; $pi<=5; $pi++) {
                factory(Property::class)->create([
                    'state_id' => $stateId,
                ]);
            }
        }

        //15 Properties in Vancouver, WA
        for ($index=1; $index<=15; $index++) {
            factory(Property::class)->create([
                'city' => 'vancouver',
                'state_id' => 48,
            ]);
        }

        //15 Properties in Portland, OR
        for ($index=1; $index<=15; $index++) {
            factory(Property::class)->create([
                'city' => 'portland',
                'state_id' => 38,
            ]);
        }

    }
}
