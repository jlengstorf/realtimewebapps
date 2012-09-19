<?php

/**
 * Parses template files with loaded data to output HTML markup
 *
 * @author Jason Lengstorf <jason@lengstorf.com>
 */
class View
{

    protected $view,
              $vars = array();

    /**
     * Initializes the view
     *
     * @param $view array   The view slug
     * @return void
     */
    public function __construct( $view ) {
        $this->view = $view;
    }

    /**
     * Stores data for the view into an array
     *
     * @param $key string   The variable name
     * @param $var string   The variable value
     * @return void
     */
    public function __set( $key, $var ) {
        $this->vars[$key] = $var;
    }

    /**
     * Loads and parses the selected template using the provided data
     *
     * @param $print boolean    Whether the markup should be output directly
     * @return mixed            A string of markup if $print is TRUE or void
     */
    public function render( $print=TRUE ) {
        extract($this->vars);
        $view_filepath = SYS_PATH . '/views/' . $this->view . '.inc.php';

        if (!file_exists($view_filepath)) {
            throw new Exception("That view file doesn't exist.");
        }

        if (!$print) {
            ob_start();
        }

        require $view_filepath;

        if (!$print) {
            return ob_get_clean();
        }
    }

}
