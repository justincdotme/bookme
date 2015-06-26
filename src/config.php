<?php
//App Config
define('SITE_URL', 'http://bookme.dev');
define('SITE_NAME', 'BookMe');
define('ZIP_CODE', '98686');

//Database config
define('DB_HOST', 'localhost');
define('DB_NAME', 'database-name');
define('DB_USER', 'database-user');
define('DB_PASS', 'database-password');

//Auth config
define('INVALID_USER_ERROR', 'Invalid username/password');
define('POST_LOGOUT_URL', '/admin/login');
define('POST_LOGIN_URL', '/admin');
define('LOGIN_URL', '/admin/login');

//User config
define('POST_REGISTER_URL', '/admin/users');
define('POST_DELETE_USER_URL', '/admin/users');

//Property config
define('POST_ADD_PROPERTY_URL', '/admin/properties');
define('POST_DELETE_PROPERTY_URL', '/admin/properties');
define('POST_UPDATE_PROPERTY_URL', '/admin/properties');
define('DEFAULT_PRICE_TEXT', 'Call for rate!');
define('MISSING_DESC_TEXT', 'No description available.');

//Administrator config
define('ADMIN_NAME', 'Demo User');
define('ADMIN_EMAIL', 'demo.user@justinc.me');

//Email config
define('REPLY_TO', 'server@justinc.me');
define('RETURN_PATH', 'server@justinc.me');
define('EMAIL_FROM', 'server@justinc.me');

//Upload config
define('PROPERTY_IMG_TMP_DIR', '/images/properties/tmp');
define('PROPERTY_IMG_UPLOAD_DIR', '/images/properties');
define('GENERIC_UPLOAD_ERROR_MESSAGE', 'An error has occurred, please try again.');

//Rental config
define('RENTAL_CONFIRMATION_SUBJECT','Rental Confirmation');
