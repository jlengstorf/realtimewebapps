<?php

/**
 * Creates a set of generic database interaction methods
 *
 * @author  Jason Lengstorf <jason@lengstorf.com>
 * @author  Phil Leggetter <phil@leggetter.co.uk>
 */
abstract class Model
{

    public static $db;

    /**
     * Creates a PDO connection to MySQL
     *
     * @return boolean  Returns TRUE on success (dies on failure)
     */
    public function __construct(  ) {
        $dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST;
        try {
            self::$db = new PDO($dsn, DB_USER, DB_PASS);
        } catch (PDOExeption $e) {
            die("Couldn't connect to the database.");
        }

        return TRUE;
    }

    /**
     * Performs basic input sanitization on a given string
     *
     * @param $dirty    string  The string to be sanitized
     * @return          string  The sanitized string
     */
    public function sanitize( $dirty )
    {
        return htmlentities(strip_tags($dirty), ENT_QUOTES);
    }

}
