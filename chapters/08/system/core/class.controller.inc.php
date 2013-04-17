<?php

/**
 * An abstract class that lays the groundwork for all controllers
 *
 * @author Jason Lengstorf <jason@lengstorf.com>
 * @author Phil Leggetter <phil@leggetter.co.uk>
 */
abstract class Controller
{

    public $actions = array(),
           $model;

    protected static $nonce = NULL;

    /**
     * Initializes the view
     *
     * @param $options array Options for the view
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
     * @return string The generated nonce
     */
    protected function generate_nonce( )
    {
        // Checks for an existing nonce before creating a new one
        if (empty(self::$nonce)) {
            self::$nonce = base64_encode(uniqid(NULL, TRUE));
            $_SESSION['nonce'] = self::$nonce;
        }

        return self::$nonce;
    }

    /**
     * Checks for a valid nonce
     *
     * @return bool TRUE if the nonce is valid; otherwise FALSE
     */
    protected function check_nonce( )
    {
        if (
            isset($_SESSION['nonce']) && !empty($_SESSION['nonce'])
            && isset($_POST['nonce']) && !empty($_POST['nonce'])
            && $_SESSION['nonce']===$_POST['nonce']
        ) {
            $_SESSION['nonce'] = NULL;
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Handles form submissions
     *
     * @param $action string The form action being performed
     * @return void
     */
    protected function handle_form_submission( $action )
    {
        if ($this->check_nonce()) {

            // Calls the method specified by the action
            $output = $this->{$this->actions[$action]}();

            if (is_array($output) && isset($output['room_id'])) {
                $room_id = $output['room_id'];
            } else {
                throw new Exception('Form submission failed.');
            }

            header('Location: ' . APP_URI . 'room/' . $room_id);
            exit;
        } else {
            throw new Exception('Invalid nonce.');
        }
    }

    /**
     * Performs basic input sanitization on a given string
     *
     * @param $dirty string The string to be sanitized
     * @return string The sanitized string
     */
    protected function sanitize( $dirty )
    {
        return htmlentities(strip_tags($dirty), ENT_QUOTES);
    }

    /**
     * Sets the title for the view
     *
     * @return string The text to be used in the <title> tag
     */
    abstract public function get_title( );

    /**
     * Loads and outputs the view's markup
     *
     * @return void
     */
    abstract public function output_view( );

}
