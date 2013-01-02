<?php

/**
 * Processes output for the Room view
 *
 * @author Jason Lengstorf <jason@lengstorf.com>
 */
class Notfound extends Controller
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
        return '404 Not Found';
    }

    /**
     * Loads and outputs the view's markup
     *
     * @return void
     */
    public function output_view(  )
    {
        $view = new View('notfound');

        $view->render();
    }

}
