<?php

/**
 * Processes output for the Room view
 *
 * @author Jason Lengstorf <jason@lengstorf.com>
 */
class Room
{

    public $room_id,
           $is_presenter,
           $is_active;

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

        $this->room_id = (int) $options[0];
        if ($this->room_id===0) {
            throw new Exception("Invalid room ID supplied");
        }

        $this->session = $this->get_session_data();

        $this->is_presenter = $this->is_presenter();

        $this->is_active = $this->is_active();
    }

    /**
     * Generates the title of the page
     *
     * @return string   The title of the page
     */
    public function get_title(  )
    {
        return $this->session->name . ' by ' . $this->session->presenter;
    }

    /**
     * Loads and outputs the view's markup
     *
     * @param $view string  The slug of the view
     * @return void
     */
    public function output_view( $view = 'room' )
    {
        $view = new View($view);

        //TODO Load real data from the model
        $view->session_name = $this->session->name;
        $view->presenter = $this->session->presenter;
        $view->email = $this->session->email;

        if (!$this->is_presenter) {
            $view->ask_form = $this->show_ask_form($this->session->email);
            $view->questions_class = !$this->is_active ? 'closed' : NULL;
        } else {
            $view->ask_form = NULL;
            $view->questions_class = 'presenter';
        }

        $view->controls = $this->show_presenter_controls();
        $view->questions = $this->show_questions();

        $view->render();
    }

    /**
     * Loads and formats the questions for this room
     *
     * @return string   The marked up questions
     */
    protected function show_questions(  )
    {
        //TODO Load real questions
        $questions = array(
                (object) array(
                    'question' => 'What is the best way to implement realtime features today?',
                    'question_id' => 1,
                    'vote_count' => 27,
                ),
                (object) array(
                    'question' => 'Does this work on browsers that don’t support the WebSockets API?',
                    'question_id' => 2,
                    'vote_count' => 14,
                ),
            );

        $output = NULL;
        foreach ($questions as $question) {
            $view = new View('question');
            $view->question = $question->question;
            $view->question_id = $question->question_id;
            $view->vote_count = $question->vote_count;

            if (!$this->is_presenter) {
                $view->vote_link = '<a href="#" class="vote">Vote Up</a>';
                $view->answer_link = NULL;
            } else {
                $view->vote_link = NULL;
                $view->answer_link = '<a href="#" class="answer">Answer</a>';
            }

            $output .= $view->render(FALSE);
        }

        return $output;
    }

    /**
     * Shows the "ask a question" form or a notice that the session has ended
     *
     * @param $email string The presenter's email address
     * @return string       Markup for the form or notice
     */
    protected function show_ask_form( $email )
    {
        ob_start();

        if ($this->is_active):
?> 

    <form id="ask-a-question">
        <label>
            If you have a question and you don’t see it below, ask it here.
            <input type="text" name="new-question" tabindex="1" />
        </label>
        <input type="submit" value="Ask" tabindex="2" />
    </form><!--/#ask-a-question-->

<?php   else: // If the session is over, shows a message ?> 

    <h3>This session has ended.</h3>
    <p>
        If you have a question that wasn't answered, please 
        <a href="mailto:<?php echo $email; ?>">email the presenter</a>.
    </p>

<?php
        endif;

        return ob_get_clean();
    }

    /**
     * Shows the presenter his controls (or nothing, if not the presenter)
     *
     * @return mixed    Markup for the controls (or NULL)
     */
    protected function show_presenter_controls(  )
    {
        $controls = NULL;

        if ($this->is_presenter):
            ob_start();
?> 
        <form id="close-this-session">
            <label>
                Link to your session.
                <input type="text" name="session-url" 
                       value="http://rwaapp.com/1234" disabled />
            </label>
            <input type="submit" value="End This Session" />
        </form><!--/#close-this-session-->
<?php
        endif;

        $controls = ob_get_clean();
        return $controls;
    }

    /**
     * Loads information about the session
     *
     * @return object   The session data
     */
    protected function get_session_data(  )
    {
        //TODO: Load real session data from the DB
        $session = (object) array(
            'session_id' => 1234,
            'name'       => 'Realtime Web Apps &amp; the Mobile Internet',
            'presenter'  => 'Jason Lengstorf',
            'email'      => 'jason@lengstorf.com',
            'active'     => 1,
        );

        return $session;
    }

    /**
     * Determines whether or not the current user is the presenter
     *
     * @return boolean  TRUE if it's the presenter, otherwise FALSE
     */
    protected function is_presenter(  )
    {
        //TODO: Use session and/or cookies to identify the presenter
        return isset($_GET['p']) ? (boolean) $_GET['p'] : FALSE;
    }

    /**
     * Determines whether or not the session is active
     *
     * @return boolean  TRUE if it's active, otherwise FALSE
     */
    protected function is_active(  )
    {
        //TODO: Get sessions active status from the DB
        return isset($_GET['a']) ? (boolean) $_GET['a'] : TRUE;
    }

}
