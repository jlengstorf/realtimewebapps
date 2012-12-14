<?php

/**
 * A sample configuration file
 *
 * The variables below need to be filled out with environment specific data.
 *
 * @author  Jason Lengstorf <jason@lengstorf.com>
 */


// Set up an array for constants
$_C = array();


//-----------------------------------------------------------------------------
// General configuration options
//-----------------------------------------------------------------------------

$_C['APP_TIMEZONE'] = 'US/Pacific';


//-----------------------------------------------------------------------------
// Database credentials
//-----------------------------------------------------------------------------

$_C['DB_HOST'] = 'localhost';
$_C['DB_NAME'] = '';
$_C['DB_USER'] = '';
$_C['DB_PASS'] = '';


//-----------------------------------------------------------------------------
// Pusher credentials
//-----------------------------------------------------------------------------

$_C['PUSHER_KEY']    = '';
$_C['PUSHER_SECRET'] = '';
$_C['PUSHER_APPID']  = '';


//-----------------------------------------------------------------------------
// Enable debug mode (strict error reporting)
//-----------------------------------------------------------------------------

$_C['DEBUG'] = TRUE;


//-----------------------------------------------------------------------------
// Converts the constants array into actual constants
//-----------------------------------------------------------------------------

foreach ($_C as $constant=>$value) {
    define($constant, $value);
}
