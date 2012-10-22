<?php

// Starts the session
if (!isset($_SESSION)) {
    session_start();
}

require_once dirname(__FILE__) . '/system/config/config.inc.php';

// Makes sure the
if (
    isset($_SESSION['nonce']) && !empty($_SESSION['nonce']) 
    && isset($_POST['nonce']) && !empty($_POST['nonce']) 
    && $_SESSION['nonce']===$_POST['nonce']
) {
    // Remove the nonce from the session for security
    $_SESSION['nonce'] = NULL;

    // Sanitizes the form action
    $action = preg_replace('#[^a-z0-9]#', '', $_POST['action']);

    // Defines available actions and how to process each
    $actions = array(
        'create' => (object) array(
            'class'  => 'Room_Model',
            'method' => 'create_room',
        ),
    );

    // Makes sure there's a handler in place for the requested action
    if (array_key_exists($action, $actions)) {
        $model = new $actions[$action]->class;
        $model->{$actions[$action]->method}();
    }
} else {
    // Bounces the user back to the home page if nonces don't match
    // header('Location: ./');
    echo '<pre>Nonce mismatch.', "\n", $_SESSION['nonce'], "\n", $_POST['nonce'], '</pre>';
    exit;
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
        dirname(__FILE__) . '/system/models/class.' . $fname . '.inc.php',
        dirname(__FILE__) . '/system/lib/class.' . $fname . '.inc.php',
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
