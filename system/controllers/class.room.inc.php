<?php

/**
 * Processes output for the Room view
 *
 * @author Jason Lengstorf <jason@lengstorf.com>
 */
class Room extends Controller
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

        $this->room         = $this->get_room_data();
        $this->is_presenter = $this->is_presenter();
        $this->is_active    = (boolean) $this->room->is_active;
    }

    /**
     * Generates the title of the page
     *
     * @return string   The title of the page
     */
    public function get_title(  )
    {
        return $this->room->room_name . ' by ' . $this->room->presenter_name;
    }

    /**
     * Loads and outputs the view's markup
     *
     * @param $view string  The slug of the view
     * @return void
     */
    public function output_view(  )
    {
        $view = new View('room');
        $view->room_id   = $this->room->room_id;
        $view->room_name = $this->room->room_name;
        $view->presenter = $this->room->presenter_name;
        $view->email     = $this->room->email;

        if (!$this->is_presenter) {
            $view->ask_form = $this->show_ask_form();
            $view->questions_class = NULL;
        } else {
            $view->ask_form = NULL;
            $view->questions_class = 'presenter';
        }

        if (!$this->is_active) {
            $view->questions_class = 'closed';
        }

        $view->controls  = $this->show_presenter_controls();
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
        $questions = $this->get_questions();

        $output = NULL;
        foreach ($questions as $question) {

            /*
             * Questions have their own view type, so this section initializes
             * and sets up variables for the question view
             */
            $view = new View('question');
            $view->question     = $question->question;
            $view->room_id      = $this->room->room_id;
            $view->question_id  = $question->question_id;
            $view->vote_count   = $question->vote_count;

            if ($question->is_answered==1) {
                $view->answered_class = 'answered';
            } else {
                $view->answered_class = NULL;
            }

            // Checks if the user has already voted up this question
            $cookie = 'voted_for_' . $question->question_id;
            if (isset($_COOKIE[$cookie]) && $_COOKIE[$cookie]==1) {
                $view->voted_class = 'voted';
            } else {
                $view->voted_class = NULL;
            }

            $view->vote_link = $this->show_vote_form(
                $question->question_id,
                $question->is_answered
            );

            $view->answer_link = $this->show_answer_form(
                $question->question_id
            );

            $output .= $view->render(FALSE);
        }

        return $output;
    }

    /**
     * Shows the "ask a question" form or a notice that the room has ended
     *
     * @param $email string The presenter's email address
     * @return string       Markup for the form or notice
     */
    protected function show_ask_form(  )
    {
        if ($this->is_active) {
            $view           = new View('ask-form');
            $view->room_id  = $this->room->room_id;
            $view->nonce    = $this->generate_nonce();

            return $view->render(FALSE);
        } else {
            $view = new View('room-closed');
            $view->email = $this->room->email;

            return $view->render(FALSE);
        }
    }

    /**
     * Generates the voting form for attendees
     *
     * @param $question_id  int     The ID of the question
     * @param $answered     int     1 if answered, 0 if unanswered
     * @return              mixed   Markup if attendee, NULL if presenter
     */
    protected function show_vote_form( $question_id, $answered )
    {
        if (!$this->is_presenter) {
            $view = new View('question-vote');
            $view->room_id      = $this->room->room_id;
            $view->question_id  = $question_id;
            $view->nonce        = $this->generate_nonce();
            $view->disabled     = $answered==1 ? 'disabled' : NULL;

            return $view->render(FALSE);
        }

        return NULL;
    }

    /**
     * Generates the answering form for presenter
     *
     * @param $question_id  int     The ID of the question
     * @return              mixed   Markup if presenter, NULL if attendee
     */
    protected function show_answer_form( $question_id )
    {
        if ($this->is_presenter) {
            $view = new View('question-answer');
            $view->room_id      = $this->room->room_id;
            $view->question_id  = $question_id;
            $view->nonce        = $this->generate_nonce();

            return $view->render(FALSE);
        }

        return NULL;
    }

    /**
     * Shows the presenter his controls (or nothing, if not the presenter)
     *
     * @return mixed    Markup for the controls (or NULL)
     */
    protected function show_presenter_controls(  )
    {
        if ($this->is_presenter) {
            if (!$this->is_active) {
                $view_class = 'presenter-reopen';
            } else {
                $view_class = 'presenter-controls';
            }

            $view = new View($view_class);
            $view->room_id = $this->room->room_id;
            $view->nonce = $this->generate_nonce();

            return $view->render(FALSE);
        }

        return NULL;
    }

    /**
     * Loads information about the room
     *
     * @return object   The room data
     */
    protected function get_room_data(  )
    {
        $model = new Room_Model;
        return $model->get_room_data($this->room_id);
    }

    /**
     * Loads questions for the room
     *
     * @return array   The question data as an array of objects
     */
    protected function get_questions(  )
    {
        $model = new Question_Model;
        return $model->get_room_questions($this->room_id);
    }

    /**
     * Determines whether or not the current user is the presenter
     *
     * @return boolean  TRUE if it's the presenter, otherwise FALSE
     */
    protected function is_presenter(  )
    {
        $cookie = 'presenter_room_' . $this->room->room_id;
        return (isset($_COOKIE[$cookie]) && $_COOKIE[$cookie]==1);
    }

}
