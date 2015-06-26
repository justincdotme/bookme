<?php
require dirname(__FILE__) . '/../vendor/autoload.php';
require dirname(__FILE__) . '/../src/config.php';

use bookMe\Model\User;
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule();

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => DB_HOST,
    'database'  => DB_NAME,
    'username'  => DB_USER,
    'password'  => DB_PASS,
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
    'strict'    => false,
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();


/**
 * Run the database migrations.
 *
 */

//Create the properties table.
if(!$capsule->schema()->hasTable('properties'))
{
    Capsule::schema()->create('properties', function($table)
    {
        $table->increments('pid');
        $table->string('name');
        $table->decimal('rate', 19, 2)->nullable();
        $table->text('short_desc');
        $table->text('long_desc');
    });
}

//Create the property_images table.
if(!$capsule->schema()->hasTable('property_images'))
{
    Capsule::schema()->create('property_images', function($table)
    {
        $table->increments('img_id');
        $table->integer('pid')->unsigned()->index();
        $table->foreign('pid')->references('pid')->on('properties')->onDelete('cascade');
        $table->string('image_full_path');
    });
}

//Create the users table.
if(!$capsule->schema()->hasTable('users'))
{
    Capsule::schema()->create('users', function($table)
    {
        $table->increments('uid');
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password', 60);
    });
}

//Create the customers table.
if(!$capsule->schema()->hasTable('customers'))
{
    Capsule::schema()->create('customers', function($table)
    {
        $table->increments('cid');
        $table->string('first_name');
        $table->string('last_name');
        $table->string('email_address');
        $table->string('addr_street_1');
        $table->string('addr_street_2')->nullable();
        $table->string('addr_city');
        $table->string('addr_state');
        $table->integer('addr_zip');
        $table->bigInteger('home_phone');
    });
}

//Create the reservations table.
if(!$capsule->schema()->hasTable('reservations'))
{
    Capsule::schema()->create('reservations', function($table)
    {
        $table->increments('rid');
        $table->integer('pid')->unsigned()->index();
        $table->foreign('pid')->references('pid')->on('properties')->onDelete('cascade');
        $table->integer('cid')->unsigned()->index();
        $table->foreign('cid')->references('cid')->on('customers')->onDelete('cascade');
        $table->integer('guests');
        $table->integer('status');
        $table->date('check_in');
        $table->date('check_out');
    });
}
echo 'Database tables created!' . PHP_EOL . '<br />';

/**
 * Seed the database tables.
 *
 */
//Seed the users table
(new User(
    [
        'name' => ADMIN_NAME,
        'email' => ADMIN_EMAIL,
        'password' => password_hash('demo123', PASSWORD_BCRYPT)
    ]
))->save();

echo 'Users table seeded!' . PHP_EOL . '<br />';