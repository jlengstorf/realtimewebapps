<?php

//-----------------------------------------------------------------------------
// Initializes app settings and variables
//-----------------------------------------------------------------------------

// Defines site-wide constants
define('APP_PATH',   dirname(__FILE__));
define('APP_FOLDER', dirname($_SERVER['SCRIPT_NAME']));
define('APP_URL',    'http://' . $_SERVER['SERVER_NAME'] . APP_FOLDER);
define('SYS_PATH',   APP_PATH . '/system');

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

// Sets the timezone
date_default_timezone_set(APP_TIMEZONE);

// Loads required files
require_once SYS_PATH . '/lib/class.db.inc.php';
require_once SYS_PATH . '/lib/Pusher.php';
require_once SYS_PATH . '/helper/class.view.inc.php';


//-----------------------------------------------------------------------------
// Loads and processes view data
//-----------------------------------------------------------------------------

// Parses the URL
$url_array  = read_url();
$class_name = get_controller_classname(&$url_array);
$options    = $url_array;

if (empty($class_name)) {
    $class_name = 'Home';
}

$controller = new $class_name($options);

// View-specific variables
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
    );

    // Loops through the location array and checks for a file to load
    foreach ($possible_locations as $loc) {
        if (file_exists($loc)) {
            require_once $loc;
            return TRUE;
        }
    }

    // Fails because no class was
    throw new Exception("Class $class_name wasn't found.");
}
