<?php

class Room_Model extends Db
{

    public function create_room(  )
    {
        $presenter = htmlentities($_POST['presenter-name'], ENT_QUOTES);
        $email = htmlentities($_POST['presenter-email'], ENT_QUOTES);
        $name = htmlentities($_POST['session-name'], ENT_QUOTES);

        $sql = 'INSERT INTO rooms (name) VALUES (:name)';
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR, 255);
        $stmt->execute();
        $stmt->closeCursor();

        // Get the generated room ID
        $room_id = self::$db->lastInsertId();

        $sql = "INSERT INTO presenters (name, email) 
                VALUES (:name, :email)
                ON DUPLICATE KEY UPDATE name=:name";
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':name', $presenter, PDO::PARAM_STR, 255);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $stmt->execute();
        $stmt->closeCursor();

        // Get the generated presenter ID
        $sql = "SELECT id 
                FROM presenters 
                WHERE email=:email";
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $stmt->execute();
        $pres_id = $stmt->fetch(PDO::FETCH_OBJ)->id;
        var_dump($pres_id);
        $stmt->closeCursor();

        $sql = 'INSERT INTO room_owners (room_id, presenter_id) 
                VALUES (:room_id, :pres_id)';
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(":room_id", $room_id, PDO::PARAM_INT);
        $stmt->bindParam(":pres_id", $pres_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();

        echo "Created a new room with the ID ", $room_id, " and the presenter ID ", $pres_id, ".";
    }

}
