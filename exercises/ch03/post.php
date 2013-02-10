<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'lib/Pusher.php';

// Make sure you grab your own Pusher app credentials!
$key    = '1507a86011e47d3d00ad';
$secret = 'badd14bcd1905e47b370';
$app_id = '22052';
$pusher = new Pusher($key, $secret, $app_id);

if (isset($_POST['name']) && isset($_POST['message']))
{
    $data = array(
            'name' => htmlentities(strip_tags($_POST['name'])),
            'msg'  => htmlentities(strip_tags($_POST['message'])),
        );

    $pusher->trigger('exercise-3-2', 'send-message', $data);
}
