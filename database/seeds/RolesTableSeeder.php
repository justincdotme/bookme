<?php

use App\Core\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'id' => 1,
            'name' => 'standard'
        ]);

        Role::create([
            'id' => 2,
            'name' => 'admin'
        ]);
    }
}
