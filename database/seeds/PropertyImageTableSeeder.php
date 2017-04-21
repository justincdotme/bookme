<?php

use App\Core\Property\PropertyImage;
use Illuminate\Database\Seeder;

class PropertyImageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (range(1, 285) as $i) {
            foreach (range(1, 6) as $ii) {
                PropertyImage::create([
                    'property_id' => $i,
                    'thumb_path' => "/images/temp-homes/home-{$ii}.jpg",
                    'full_path' => "/images/temp-homes/home-{$ii}.jpg"
                ]);
            }
        }
    }
}
