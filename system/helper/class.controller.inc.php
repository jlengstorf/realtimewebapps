<?php

/**
 * An abstract class that lays the groundwork for all controllers
 *
 * @author Jason Lengstorf <jason@lengstorf.com>
 */
abstract class Controller
{

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
     * Generates a random string that helps prevent XSS and duplicate submissions
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

    /**
     * Sets the title for the view
     *
     * @return string   The text to be used in the <title> tag
     */
    abstract public function get_title(  );

}
