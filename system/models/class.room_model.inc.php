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
     * @param   $presenter  string  The name of the presenter
     * @param   $email      string  The presenter's email address
     * @param   $name       string  The name of the room
     * @return              array   An array of data about the room
     */
    public function create_room( $presenter, $email, $name )
    {
        $presenter = $this->sanitize($presenter);
        $email     = $this->sanitize($email);
        $name      = $this->sanitize($name);

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

        return array(
            'room_id' => $room_id,
        );
    }

    /**
     * Checks if a given room exists
     *
     * @param   $room_id    int     The ID of the room being checked
     * @return              bool    Whether or not the room exists
     */
    public function room_exists( $room_id )
    {
        $room_id = $this->sanitize($room_id);

        // Loads the number of rooms matching the provided room ID
        $sql = "SELECT COUNT(id) AS the_count FROM rooms WHERE id = :room_id";
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);
        $stmt->execute();
        $room_exists = (bool) $stmt->fetch(PDO::FETCH_OBJ)->the_count;
        $stmt->closeCursor();

        return $room_exists;
    }

    /**
     * Sets a given room's status to "open"
     *
     * @param   $room_id    int     The ID of the room being checked
     * @return              array   An array of data about the room
     */
    public function open_room( $room_id )
    {
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
     * @param   $room_id    int     The ID of the room being checked
     * @return              array   An array of data about the room
     */
    public function close_room( $room_id )
    {
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
     * @param   $room_id    int     The ID of the room being checked
     * @return              array   An array of data about the room
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
