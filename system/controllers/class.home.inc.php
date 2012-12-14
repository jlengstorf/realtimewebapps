<?php

/**
 * Generates output for the Home view
 *
 * @author Jason Lengstorf <jason@lengstorf.com>
 */
class Home extends Controller
{

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
    public function output_view(  )
    {
        $view = new View('home');
        $view->nonce = $this->generate_nonce();

        $view->render();
    }

}
