<?php

/**
 * An abstract class that lays the groundwork for all controllers
 *
 * @author Jason Lengstorf <jason@lengstorf.com>
 */
abstract class Controller
{

    public $actions = array(),
           $model;

    protected static $nonce = NULL;

    /**
     * Initializes the view
     *
     * @param $options array    Options for the view
     * @return void
     */
    public function __construct( $options )
    {
        if (!is_array($options)) {
            throw new Exception("No options were supplied for the room.");
        }
    }

    /**
     * Generates a nonce that helps prevent XSS and duplicate submissions
     *
     * @return string   The generated nonce
     */
    protected function generate_nonce(  )
    {
        // Checks for an existing nonce before creating a new one
        if (empty(self::$nonce)) {
            self::$nonce = base64_encode(uniqid(NULL, TRUE));
            $_SESSION['nonce'] = self::$nonce;
        }

        return self::$nonce;
    }

    protected function check_nonce(  )
    {
        if (
            isset($_SESSION['nonce']) && !empty($_SESSION['nonce']) 
            && isset($_POST['nonce']) && !empty($_POST['nonce']) 
            && $_SESSION['nonce']===$_POST['nonce']
        ) {
            return TRUE;
        } else {
            throw new Exception('Invalid nonce.');
            
        }
    }

    protected function handle_form_submission( $action )
    {
        if ($this->check_nonce()) {
            $output = $this->{$this->actions[$action]}();

            if (is_array($output) && isset($output['room_id'])) {
                $room_id = $output['room_id'];
            } else {
                echo '<pre>Method: ', $this->actions[$action], "\n", print_r($output, TRUE), '</pre>';
                throw new Exception('Something went wrong.');
            }

            // Realtime stuff happens here
            $pusher = new Pusher(PUSHER_KEY, PUSHER_SECRET, PUSHER_APPID);
            $channel = 'room_' . $room_id;
            $pusher->trigger($channel, $action, $output);

            header('Location: ' . APP_URI . 'room/' . $room_id);
            exit;
        }
    }

    /**
     * Sets the title for the view
     *
     * @return string   The text to be used in the <title> tag
     */
    abstract public function get_title(  );

    /**
     * Loads and outputs the view's markup
     *
     * @return void
     */
    abstract public function output_view(  );

}
