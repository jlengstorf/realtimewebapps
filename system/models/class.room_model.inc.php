<?php

/**
 * Creates database interaction methods for rooms
 *
 * @author Jason Lengstorf <jason@lengstorf.com>
 */
class Room_Model extends Model
{

    /**
     * Saves a new room to the database
     *
     * @return  void
     */
    public function create_room(  )
    {
        $presenter = $this->sanitize($_POST['presenter-name']);
        $email = $this->sanitize($_POST['presenter-email']);
        $name = $this->sanitize($_POST['session-name']);

        // Creates a new room
        $sql = 'INSERT INTO rooms (name) VALUES (:name)';
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR, 255);
        $stmt->execute();
        $stmt->closeCursor();

        // Gets the generated room ID
        $room_id = self::$db->lastInsertId();

        // Creates (or updates) the presenter
        $sql = "INSERT INTO presenters (name, email) 
                VALUES (:name, :email)
                ON DUPLICATE KEY UPDATE name=:name";
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':name', $presenter, PDO::PARAM_STR, 255);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $stmt->execute();
        $stmt->closeCursor();

        // Gets the generated presenter ID
        $sql = "SELECT id 
                FROM presenters 
                WHERE email=:email";
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $stmt->execute();
        $pres_id = $stmt->fetch(PDO::FETCH_OBJ)->id;
        $stmt->closeCursor();

        // Stores the room:presenter relationship
        $sql = 'INSERT INTO room_owners (room_id, presenter_id) 
                VALUES (:room_id, :pres_id)';
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(":room_id", $room_id, PDO::PARAM_INT);
        $stmt->bindParam(":pres_id", $pres_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();

        // Makes the creator of this room its presenter
        setcookie('presenter_room_' . $room_id, 1, time() + 2592000, '/');

        // Sends the presenter to the newly created room
        header("Location: ./room/" . $room_id);
        exit;
    }

    /**
     * Sends an attendee to a given room if it exists
     *
     * @return  void
     */
    public function join_room(  )
    {
        $room_id = $this->sanitize($_POST['room_id']);

        // Loads the number of rooms matching the provided room ID
        $sql = "SELECT COUNT(id) AS the_count FROM rooms WHERE id = :room_id";
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);
        $stmt->execute();
        $room_exists = (bool) $stmt->fetch(PDO::FETCH_OBJ)->the_count;
        $stmt->closeCursor();

        // If the room exists, creates the URL; otherwise, sends to a 404
        if ($room_exists) {
            $header = './room/' . $room_id;
        } else {
            $header = './no-room';
        }

        header("Location: " . $header);
        exit;
    }

    /**
     * Sets a given room's status to "open"
     *
     * @return  array   The ID of the room
     */
    public function open_room(  )
    {
        $room_id = (int) $_POST['room_id'];

        $sql = "UPDATE rooms SET is_active=1 WHERE id = :room_id";
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();

        return array(
            'room_id' => $room_id,
        );
    }

    /**
     * Sets a given room's status to "closed"
     *
     * @return  array   The ID of the room
     */
    public function close_room(  )
    {
        $room_id = (int) $_POST['room_id'];

        $sql = "UPDATE rooms SET is_active=0 WHERE id = :room_id";
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();

        return array(
            'room_id' => $room_id,
        );
    }

    /**
     * Retrieves details about a given room
     *
     * @param   $room_id    int     The ID of the room
     * @return              array   The room's data
     */
    public function get_room_data( $room_id )
    {
        $sql = "SELECT 
                    rooms.id AS room_id, 
                    presenters.id AS presenter_id, 
                    rooms.name AS room_name,
                    presenters.name AS presenter_name,
                    email, is_active
                FROM rooms 
                LEFT JOIN room_owners 
                    ON( rooms.id = room_owners.room_id )
                LEFT JOIN presenters 
                    ON( room_owners.presenter_id = presenters.id )
                WHERE rooms.id = :room_id
                LIMIT 1";
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);
        $stmt->execute();
        $room_data = $stmt->fetch(PDO::FETCH_OBJ);
        $stmt->closeCursor();

        return $room_data;
    }

}
