<?php

use App\Core\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
           'first_name' => 'Foo',
           'last_name' => 'McBar',
           'email' => 'info@justinc.me',
           'password' => bcrypt('staging'),
           'role_id' => 2
        ]);
    }
}
