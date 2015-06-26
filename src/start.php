<?php

use bookMe\lib\DataAccess\Database;
use bookMe\lib\Http\Session;

//Load the class autoloader
require dirname(__FILE__) . '/../vendor/autoload.php';

//Start the session handler
Session::start();

//Load config settings
require dirname(__FILE__) . '/../src/config.php';

//Start Eloquent
Database::start();

//Finally, load the routes list
require dirname(__FILE__) . '/../src/routes.php';