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

        $this->model = new Room_Model;

        // Checks for a form submission
        $this->actions = array(
            'join'   => 'join_room',
            'create' => 'create_room',
            'open'   => 'open_room',
            'close'  => 'close_room',
        );

        if (array_key_exists($options[0], $this->actions)) {
            $this->handle_form_submission($options[0]);
            exit;
        } else {
            $this->room_id = isset($options[0]) ? (int) $options[0] : 0;
            if ($this->room_id===0) {
                throw new Exception("Invalid room ID supplied");
            }
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
            $view->ask_form = $this->output_ask_form();
            $view->questions_class = NULL;
        } else {
            $view->ask_form = NULL;
            $view->questions_class = 'presenter';
        }

        if (!$this->is_active) {
            $view->questions_class = 'closed';
        }

        $view->controls  = $this->output_presenter_controls();
        $view->questions = $this->output_questions();

        $view->render();
    }

    /**
     * Loads and formats the questions for this room
     *
     * @return string   The marked up questions
     */
    protected function output_questions(  )
    {
        $controller = new Question(array($this->room_id));

        // Allows for different output for presenters vs. attendees
        $controller->is_presenter = $this->is_presenter;

        return $controller->output_view();
    }

    /**
     * Shows the "ask a question" form or a notice that the room has ended
     *
     * @param $email string The presenter's email address
     * @return string       Markup for the form or notice
     */
    protected function output_ask_form(  )
    {
        $controller = new Question(array($this->room_id));
        return $controller->output_ask_form(
            $this->is_active, 
            $this->room->email
        );
    }

    /**
     * Shows the presenter his controls (or nothing, if not the presenter)
     *
     * @return mixed    Markup for the controls (or NULL)
     */
    protected function output_presenter_controls(  )
    {
        if ($this->is_presenter) {
            if (!$this->is_active) {
                $view_class  = 'presenter-reopen';
                $form_action = APP_URI . 'room/open';
            } else {
                $view_class  = 'presenter-controls';
                $form_action = APP_URI . 'room/close';
            }

            $view = new View($view_class);
            $view->room_id     = $this->room->room_id;
            $view->room_uri    = APP_URI . 'room/' . $this->room_id;
            $view->form_action = $form_action;
            $view->nonce       = $this->generate_nonce();

            return $view->render(FALSE);
        }

        return NULL;
    }

    /**
     * Checks if a room exists and redirects the user appropriately
     *
     * @return void
     */
    protected function join_room(  )
    {
        $room_id = $_POST['room_id'];

        // If the room exists, creates the URL; otherwise, sends to a 404
        if ($this->model->room_exists($room_id)) {
            $header = APP_URI . 'room/' . $room_id;
        } else {
            $header = APP_URI . 'no-room';
        }

        header("Location: " . $header);
        exit;
    }

    /**
     * Creates a new room and sets the creator as the presenter
     *
     * @return array Information about the updated room
     */
    protected function create_room(  )
    {
        $presenter = $_POST['presenter-name'];
        $email     = $_POST['presenter-email'];
        $name      = $_POST['session-name'];

        // Store the new room and its various associations in the database
        $output = $this->model->create_room($presenter, $email, $name);

        // Make sure valid output was returned
        if (is_array($output) && isset($output['room_id'])) {
            $room_id = $output['room_id'];
        } else {
            throw new Exception('Error creating the room.');
        }

        // Makes the creator of this room its presenter
        setcookie('presenter_room_' . $room_id, 1, time() + 2592000, '/');

        return $output;
    }

    /**
     * Marks a given room as active
     *
     * @return array Information about the updated room
     */
    protected function open_room(  )
    {
        $room_id = (int) $_POST['room_id'];
        return $this->model->open_room($room_id);
    }

    /**
     * Marks a given room as closed
     *
     * @return array Information about the updated room
     */
    protected function close_room(  )
    {
        $room_id = (int) $_POST['room_id'];
        return $this->model->close_room($room_id);
    }

    /**
     * Loads information about the room
     *
     * @return object   The room data
     */
    protected function get_room_data(  )
    {
        return $this->model->get_room_data($this->room_id);
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
