<?php
    // DB Params
    define('DB_HOST', '127.0.0.1');
    define('DB_USER', 'root');
    define('DB_PASS', "root");
    define('DB_NAME', 'curriculum_generator_app');
    /* 
        App Root - whatever the path of the project folder on the machine is + \app folder. 
        dirname(dirname(__FILE__)) - two folders back from the current (config) file, should also work
    */
    define('APPROOT',  dirname(dirname(__FILE__)));    
    define('URLROOT', 'http://localhost/curriculum_generator');
    define('SITENAME', 'Curriculum Generator & Viewer');
	define('SITE_FN', 82150);
	define('SITE_CREATOR', 'Petar Petrov');
	define('SITE_ADMIN_EMAIL', 'pgpetrov2001@gmail.com');
	define('SITE_INFO', 'This project was created during 2024, on Web Technologies, Sofia University, FMI, lead by: Milen Petrov, assistant: Mihail Georgiev');
  /* $SITE_DESCRIPTION="What is ready, and what can be improved for future"; */

    define('APPVERSION', '1.0.0');
?>
