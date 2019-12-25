<?php

namespace BertMaurau\URLShortener\Core;

/**
 * Description of Database
 *
 * This handles everything concerning the Database connection.
 *
 * @author Bert Maurau
 */
class Database
{

    // holds the connection with the database
    private static $mysqli;
    private static $name;
    private static $host;
    private static $user;
    private static $pass;

    /**
     * Init a new connection
     *
     * @throws Exception
     */
    static function init($connectWithDatabase = true)
    {

        self::$name = Config::getInstance() -> Database() -> name;
        self::$host = Config::getInstance() -> Database() -> host;
        self::$user = Config::getInstance() -> Database() -> user;
        self::$pass = Config::getInstance() -> Database() -> pass;

        // connect with the database
        if (!self::$mysqli = new \mysqli(self::$host, self::$user, self::$pass, ($connectWithDatabase) ? self::$name : null)) {
            throw new Exception("Failed to connect with the Database.");
        }


        // Set the charset to allow for example emoticons
        self::$mysqli -> set_charset(Config::getInstance() -> Database() -> charset);
    }

    /**
     * Escape the given value
     *
     * @param any $value The value to escape
     *
     * @return any The escaped value
     */
    public static function escape($value)
    {
        // fallback
        if (is_object($value) || is_array($value)) {
            if ($value instanceof \DateTime) {
                $value = $value -> format('Y-m-d H:i:s');
            } else {
                // just encode it..
                $value = json_encode($value);
            }
        }
        return self::$mysqli -> real_escape_string($value);
    }

    /**
     * Get the last inserted ID
     *
     * @return integer The ID
     */
    public static function getId()
    {
        return self::$mysqli -> insert_id;
    }

    /**
     * Get the amount of affected rows
     *
     * @return integer The amount of affected rows
     */
    public static function getAffectedRows()
    {
        return self::$mysqli -> affected_rows;
    }

    /**
     * Execute the given query
     *
     * @param string $query The query to execute
     *
     * @return resultset
     */
    public static function query(string $query)
    {
        if (!$result = self::$mysqli -> query($query)) {
            throw new \Exception("\r\n\Failed to execute query: $query \r\n" . self::getLastError());
        }
        return $result;
    }

    /**
     * Close the connection (if open)
     */
    public static function close()
    {
        if (self::$mysqli) {
            self::$mysqli -> close();
        }
    }

    /**
     * Return the last mysqli error
     *
     * @return string The last error message
     */
    public static function getLastError()
    {
        return self::$mysqli -> error;
    }

    /**
     * Return the current database
     *
     * @return string The database name
     */
    public static function getDatabase()
    {
        return self::$name;
    }

}
