<?php

/**
 * Generates output for the Home view
 *
 * @author Jason Lengstorf <jason@lengstorf.com>
 */
class Home
{

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
     * Generates the title of the page
     *
     * @return string   The title of the page
     */
    public function get_title(  )
    {
        return 'Realtime Q&amp;A';
    }

    /**
     * Loads and outputs the view's markup
     *
     * @param $view string  The slug of the view
     * @return void
     */
    public function output_view( $view = 'home' )
    {
        $view = new View($view);

        // Generate a nonce for security purposes
        $nonce = base64_encode(uniqid(NULL, TRUE));
        $view->nonce = $_SESSION['nonce'] = $nonce;

        $view->render();
    }

}
