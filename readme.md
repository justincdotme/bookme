# bookMe
 bookMe is a PHP5 application for booking and managing rental property reservations. 
 
 The goal of this project was to create a fully functional property booking application from scratch using MVC architecture.
 
 The following features were required for this project:
 
  - Fully functional administration panel
  - Property management module
  - Property image management
  - Text editor for property descriptions
  - Reservation management module
  - User management module
  
 **View Demo**
 
 [http://bookme.demos.justinc.me](http://bookme.demos.justinc.me)
 
 **Username:** demo.user@justinc.me
 
 **Password:** demo123
 
 

## Requirements
 - This application requires the Composer Dependency Manager, located at [getcomposer.org](https://getcomposer.org/)
 - This application requires MySQL 5
 - This application requires SwiftMailer
 - This application requires the Eloquent ORM

## Installation

 Clone the repository
 
    git clone https://github.com/justincdotme/bookme.git

 Install the application using Composer
 
    composer install

 Set the application URL in src/config.php
 
    define('SITE_URL', 'http://your.url.here');
    
 Set the application title (page title) in src/config.php
    
    define('SITE_NAME', 'BookMe');
    
 Set the ZIP code (used for temperature module) in src/config.php
    
    define('ZIP_CODE', '98686');
    
 Set the database credentials in src/config.php
    
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'database-name');
    define('DB_USER', 'database-username');
    define('DB_PASS', 'database-password');
      
 Set the email configuration options in src/config.php
    
    define('REPLY_TO', 'reply-to@domain.com');
    define('RETURN_PATH', 'return-path@domain.com');
    define('EMAIL_FROM', 'email-from@domain.com');
    
 Configure the SwiftMailer transport in src/lib/Email/Email.php ([Documentation](http://swiftmailer.org/docs/sending.html))
  
    $this->_transport = Swift_SmtpTransport::newInstance("smtp.mailserver.com", 25)
        ->setUsername('username')
        ->setPassword('password');
      
 Set the administrative user's name and email address in src/config.php
  
     define('ADMIN_NAME', 'Your Name');
     define('ADMIN_EMAIL', 'your@email.address');
     
 Run the database migration & seed the users table by visiting the following url in your browser
   
     http://[yourdomain]/install.php
   
 Delete the installation file after installation
 
     rm [application-dir]/public/install.php
 
 Login and change the default password. The default password is "demo123"
 
     http://[yourdomain]/admin
     Click Users > Edit Users
     

## License

 The MIT License (MIT)
 
 Copyright (c) 2015 Justin Christenson
 
 Permission is hereby granted, free of charge, to any person obtaining a copy
 of this software and associated documentation files (the "Software"), to deal
 in the Software without restriction, including without limitation the rights
 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the Software is
 furnished to do so, subject to the following conditions:
 
 The above copyright notice and this permission notice shall be included in
 all copies or substantial portions of the Software.
 
 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 THE SOFTWARE.
