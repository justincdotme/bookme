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
        for($i=1; $i<=285; $i++) { // 285 test properties
            for ($ii=1; $ii<= 6; $i++) { //6 test images total
                PropertyImage::create([
                    'property_id' => $i,
                    'thumb_path' => "/images/temp-homes/home-{$ii}.jpg",
                    'full_path' => "/images/temp-homes/home-{$ii}.jpg"
                ]);
            }

        }
    }
}
