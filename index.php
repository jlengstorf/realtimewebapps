<?php

/**
 * The initialization script for the app
 *
 * @author  Jason Lengstorf <jason@lengstorf.com>
 */


//-----------------------------------------------------------------------------
// Initializes environment variables
//-----------------------------------------------------------------------------

// Server path to this app (i.e. /var/www/vhosts/realtime/httpdocs/realtime)
define('APP_PATH',   dirname(__FILE__));

// App folder, relative from web root (i.e. /realtime)
define('APP_FOLDER', dirname($_SERVER['SCRIPT_NAME']));

// URL path to the app (i.e. http://example.org/realtime)
define(
    'APP_URL', 
    remove_double_slashes('http://' . $_SERVER['SERVER_NAME'] . APP_FOLDER)
);

// Server path to the system folder (for includes)
define('SYS_PATH',   APP_PATH . '/system');

// Relative path to the form processing script (i.e. /realtime/process.php)
define('FORM_ACTION', remove_double_slashes(APP_FOLDER . '/process.php'));


//-----------------------------------------------------------------------------
// Initializes the app
//-----------------------------------------------------------------------------

// Starts the session
if (!isset($_SESSION)) {
    session_start();
}

// Loads the configuration variables
require_once SYS_PATH . '/config/config.inc.php';

// Turns on error reporting if in debug mode
if (DEBUG===TRUE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL^E_STRICT);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Sets the timezone to avoid a notice
date_default_timezone_set(APP_TIMEZONE);


//-----------------------------------------------------------------------------
// Loads and processes view data
//-----------------------------------------------------------------------------

// Parses the URL
$url_array  = read_url();
$class_name = get_controller_classname(&$url_array);
$options    = $url_array;

// Sets a default view if nothing is passed in the URL (i.e. on the home page)
if (empty($class_name)) {
    $class_name = 'Home';
}

// Tries to initialize the requested view, or else throws a 404 error
try {
    $controller = new $class_name($options);
} catch (Exception $e) {
    header('HTTP/1.0 404 Not Found');
    $controller = new Notfound($options);
}

// Loads the <title> tag value for the view
$title = $controller->get_title();


//-----------------------------------------------------------------------------
// Outputs the view
//-----------------------------------------------------------------------------

require_once SYS_PATH . '/inc/header.inc.php';

$controller->output_view();

require_once SYS_PATH . '/inc/footer.inc.php';


//-----------------------------------------------------------------------------
// Function declarations
//-----------------------------------------------------------------------------

/**
 * Breaks the URL into an array at the slashes
 *
 * @return array  The broken up URL
 */
function read_url(  )
{
    // Removes any subfolders in which the app is installed
    $real_url = preg_replace(
            '~^'.APP_FOLDER.'~', 
            '', 
            $_SERVER['REQUEST_URI'], 
            1
        );

    $url_array = explode('/', $real_url);

    // If the first element is empty, get rid of it
    if (empty($url_array[0])) {
        array_shift($url_array);
    }

    // If the last element is empty, get rid of it
    if (empty($url_array[count($url_array)-1])) {
        array_pop($url_array);
    }

    return $url_array;
}

/**
 * Determines the controller name using the first element of the URL array
 *
 * @param $url_array array  The broken up URL
 * @return string           The controller classname
 */
function get_controller_classname( $url_array )
{
    $controller = array_shift($url_array);
    return ucfirst($controller);
}

/**
 * Removes double slashes (except in the protocol)
 *
 * @param $dirty_path string    The path to check for double slashes
 * @return string               The cleaned path
 */
function remove_double_slashes( $dirty_path )
{
    return preg_replace('~(?<!:)//~', '/', $dirty_path);
}

/**
 * Autoloads classes as they are instantiated
 * 
 * @param $class_name string    The name of the class to be loaded
 * @return bool                 Returns TRUE on success (Exception on failure)
 */
function __autoload( $class_name )
{
    $fname = strtolower($class_name);
    
    // Defines all of the valid places a class file could be stored
    $possible_locations = array(
        SYS_PATH . '/models/class.' . $fname . '.inc.php',
        SYS_PATH . '/controllers/class.' . $fname . '.inc.php',
        SYS_PATH . '/helper/class.' . $fname . '.inc.php',
    );

    // Loops through the location array and checks for a file to load
    foreach ($possible_locations as $loc) {
        if (file_exists($loc)) {
            require_once $loc;
            return TRUE;
        }
    }

    // Fails because a valid class wasn't found
    throw new Exception("Class $class_name wasn't found.");
}
