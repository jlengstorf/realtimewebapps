<?php

/**
 * Processes output for the Room view
 *
 * @author Jason Lengstorf <jason@lengstorf.com>
 * @author Phil Leggetter <phil@leggetter.co.uk>
 */
class Error extends Controller
{

    private $_message = NULL;

    /**
     * Initializes the view
     *
     * @param $options array Options for the view
     * @return void
     */
    public function __construct( $options )
    {
        if (isset($options[1])) {
            $this->_message = $options[1];
        }
    }

    /**
     * Generates the title of the page
     *
     * @return string The title of the page
     */
    public function get_title( )
    {
        return 'Something went wrong.';
    }

    /**
    * Loads and outputs the view's markup
    *
    * @return void
    */
    public function output_view( )
    {
        $view = new View('error');
        $view->message = $this->_message;
        $view->home_link = APP_URI;

        $view->render();
    }

}
