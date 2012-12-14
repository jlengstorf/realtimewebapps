<?php

/**
 * Processing script for all form submissions
 *
 * @author  Jason Lengstorf <jason@lengstorf.com>
 */


// Starts the session
if (!isset($_SESSION)) {
    session_start();
}

// Loads required files
require_once dirname(__FILE__) . '/system/config/config.inc.php';
require_once dirname(__FILE__) . '/system/lib/Pusher.php';

// Turns on error reporting if in debug mode
if (DEBUG===TRUE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL^E_STRICT);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Makes sure the nonce is set and matches the expected value
if (
    isset($_SESSION['nonce']) && !empty($_SESSION['nonce']) 
    && isset($_POST['nonce']) && !empty($_POST['nonce']) 
    && $_SESSION['nonce']===$_POST['nonce']
) {
    // Remove the nonce from the session for security
    $_SESSION['nonce'] = NULL;

    // Sanitizes the form action
    $action = preg_replace('#[^a-z0-9-]#', '', $_POST['action']);

    // Defines available actions and how to process each
    $actions = array(
        'room-create' => (object) array(
            'class'  => 'Room_Model',
            'method' => 'create_room',
        ),
        'room-join' => (object) array(
            'class'  => 'Room_Model',
            'method' => 'join_room',
        ),
        'room-open' => (object) array(
            'class'  => 'Room_Model',
            'method' => 'open_room',
        ),
        'room-close' => (object) array(
            'class'  => 'Room_Model',
            'method' => 'close_room',
        ),
        'question-create' => (object) array(
            'class'  => 'Question_Model',
            'method' => 'create_question',
        ),
        'question-vote' => (object) array(
            'class'  => 'Question_Model',
            'method' => 'vote_question',
        ),
        'question-answer' => (object) array(
            'class'  => 'Question_Model',
            'method' => 'answer_question',
        ),
    );

    // Makes sure there's a handler in place for the requested action
    if (array_key_exists($action, $actions)) {
        $model = new $actions[$action]->class;
        $output = $model->{$actions[$action]->method}();

        // Realtime stuff happens here
        $pusher = new Pusher(PUSHER_KEY, PUSHER_SECRET, PUSHER_APPID);
        $channel = 'room_' . $output['room_id'];
        $pusher->trigger($channel, $action, $output);

        header("Location: ./room/" . $output['room_id']);
        exit;
    } else {
        echo "<pre>The requested action doesn't exist.\n", print_r($_POST), "</pre>";
    }
} else {
    // Bounces the user back to the home page if nonces don't match
    header('Location: ./');
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
        dirname(__FILE__) . '/system/helper/class.' . $fname . '.inc.php',
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
