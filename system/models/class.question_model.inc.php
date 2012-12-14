<?php

/**
 * Creates database interaction methods for questions
 *
 * @author Jason Lengstorf <jason@lengstorf.com>
 */
class Question_Model extends Model
{

    /**
     * Stores a new question with all the proper associations
     *
     * @return  array   The IDs of the room and the new question
     */
    public function create_question(  )
    {
        $room_id = $this->sanitize($_POST['room_id']);
        $question = $this->sanitize($_POST['new-question']);

        // Stores the new question in the database
        $sql = "INSERT INTO questions (room_id, question) 
                VALUES (:room_id, :question)";
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':room_id', $room_id);
        $stmt->bindParam(':question', $question);
        $stmt->execute();
        $stmt->closeCursor();

        // Stores the ID of the new question
        $question_id = self::$db->lastInsertId();

        /*
         * Because creating a question counts as its first vote, this adds a 
         * vote for the question to the database
         */
        $sql = "INSERT INTO question_votes
                VALUES (:question_id, 1)";
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(":question_id", $question_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();

        // Stores a cookie so the attendee can only vote once
        setcookie('voted_for_' . $question_id, 1, time() + 2592000, '/');

        return array(
            'room_id' => $room_id,
            'question_id' => $question_id,
        );
    }

    /**
     * Increases the vote count of a given question
     *
     * @return  array   The IDs of the room and the upvoted question
     */
    public function vote_question(  )
    {
        $room_id = $this->sanitize($_POST['room_id']);
        $question_id = $this->sanitize($_POST['question_id']);

        // Makes sure the attendee hasn't already voted for this question
        $cookie_id = 'voted_for_' . $question_id;
        if (!isset($_COOKIE[$cookie_id]) || $_COOKIE[$cookie_id]!=1) {
            // Increments the vote count for the question
            $sql = "UPDATE question_votes 
                    SET vote_count = vote_count+1 
                    WHERE question_id = :question_id";
            $stmt = self::$db->prepare($sql);
            $stmt->bindParam(':question_id', $question_id, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();

            // Sets a cookie to make it harder to post multiple votes
            setcookie($cookie_id, 1, time() + 2592000, '/');
        }

        return array(
            'room_id' => $room_id,
            'question_id' => $question_id,
        );
    }

    /**
     * Returns the vote count for a given question
     *
     * @return  int The number of votes for the given question
     */
    public function get_vote_count( $question_id )
    {
        $sql = "SELECT vote_count 
                FROM question_votes 
                WHERE question_id = :question_id";
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':question_id', $question_id, PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->fetch(PDO::FETCH_OBJ)->vote_count;
        $stmt->closeCursor();

        return $count;
    }

    /**
     * Marks a given question as answered
     *
     * @return  array   The IDs of the room and the new question
     */
    public function answer_question(  )
    {
        $room_id = $this->sanitize($_POST['room_id']);
        $question_id = $this->sanitize($_POST['question_id']);

        // Makes sure the person answering the question is the presenter
        $cookie_id = 'presenter_room_' . $room_id;
        if (!isset($_COOKIE[$cookie_id]) || $_COOKIE[$cookie_id]!=1) {
            $sql = "UPDATE questions
                    SET is_answered = 1
                    WHERE id = :question_id";
            $stmt = self::$db->prepare($sql);
            $stmt->bindParam(':question_id', $question_id, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
        }

        return array(
            'room_id' => $room_id,
            'question_id' => $question_id,
        );
    }

    /**
     * Loads all questions for a given room
     *
     * @param   $room_id    int     The IDs of the room and the new question
     * @return              array   The questions attached to the room
     */
    public function get_room_questions( $room_id )
    {
        $sql = "SELECT
                    id AS question_id, 
                    room_id, 
                    question, 
                    is_answered, 
                    vote_count 
                FROM questions
                    LEFT JOIN question_votes
                        ON( questions.id = question_votes.question_id )
                WHERE room_id = :room_id
                ORDER BY is_answered, vote_count DESC";
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);
        $stmt->execute();
        $questions = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt->closeCursor();

        return $questions;
    }

}
