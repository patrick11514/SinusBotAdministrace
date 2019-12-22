<?php

/**
 * Main class for sinusbot
 * 
 * @author    patrick115 <info@patrick115.eu>
 * @copyright ©2019
 * @link      https://patrick115.eu
 * @link      https://github.com/patrick11514
 * @version   0.1.0
 * 
 */

namespace patrick115\Sinusbot;

use patrick115\Sinusbot\Error;
use patrick115\Sinusbot\Config;
use patrick115\Sinusbot\Main;
use patrick115\Sinusbot\Singleton;
use mysqli;
use Exception;

class Database extends Error
{
    use Singleton;

    /**
     * Connection to database
     * 
     * @var object
     */
    private $conn = NULL;

    /**
     * Status of connection
     * 
     * @var boolean
     */
    public static $connected = NULL;

    /**
     * Contains last database error
     * 
     * @var string
     */
    public $error;

    /**
     * Table prefix
     * 
     * @var string
     */
    protected $table_prefix;

    private $config;

    private function __construct()
    {
        $this->config = Config::init();
        $this->Connect();
    }

    /**
     * Connect to database
     * 
     */
    protected function Connect()
    {
        if ($this->conn === NULL) {

            $this->table_prefix = $this->config->getConfig("Database/prefix");

            $conn = new mysqli(
                $this->config->getConfig("Database/address")
                 . ":" . 
                $this->config->getConfig("Database/port"), 
                $this->config->getConfig("Database/username"), 
                $this->config->getConfig("Database/password"), 
                $this->config->getConfig("Database/database")
            );
            $conn->set_charset("utf8mb4");

            if (isset($conn->connect_error)) {
                $this->catchError($this->errorConvert($conn->connect_error), debug_backtrace());
            }

            $this->conn = $conn;
            self::$connected = true;
            return $conn;
        }
    }

    /**
     * Remove MYSQLInsert characters
     * 
     * @param string $string String
     * 
     */
    protected static function removeChars($string)
    {
        (new self)->conn->real_escape_string($string);
        return $string;
    }

    

    /**
     * Catch error
     * 
     * @param string $error Error to convert
     * 
     */
    private static function errorConvert($error)
    {
        $uperror = $error . PHP_EOL;
        $error = strtolower($error);
        if ($error == "php_network_getaddresses: getaddrinfo failed: no address associated with hostname") {
            $return = "Undefined host.";
        } else if (strpos($error, "access denied for user ") !== false) {
            $return = "Incorrect Login Data";
        } else if ($error == "php_network_getaddresses: getaddrinfo failed: no address associated with hostname") {
            $return = "Please use valid host";
        } else if ($error == "php_network_getaddresses: getaddrinfo failed: name or service not known") {
            $return = "Please use valid host";
        } else if (strpos($error, "unknown database") !== false) {
            $return = "Database " . explode("'", $uperror)[1] . " not found! Please create it";
        }
        return $return;
    }

    /**
     * Select rows from table
     * 
     * @param array $param Selected rows
     * @param string $table Table
     * @param string $option Option like LIMIT 1..
     * @param string $haystack 
     * @param string $needle
     * 
     */
    public function select($params, $table, $options = "", $haystack = NULL, $needle = NULL)
    {
        $table = $this->convertTableName($table);

        if (empty($params) || empty($table)) {
            $this->catchError("Empty parameter(s).", debug_backtrace());
        }
        $list = "";
        for ($i=0; $i < count($params) - 1; $i++) { 
            $list .= "`" . $params[$i] . "`, ";
        }
        $list .= "`" . $params[count($params) - 1] . "`";
        if ($haystack === NULL && $needle === NULL) {
            $command = "SELECT $list FROM `$table` $options";
        } else {
            $command = "SELECT $list FROM `$table` WHERE `$haystack` = '$needle' $options"; 
        }
        try {
            $return = $this->conn->query($command);
        } catch (Exception $e) {
            $error = $e->getMessage();
            $this->catchError($error, debug_backtrace());
        }
        return $return;
    }

    /**
     * Execute sql command
     * 
     * @param string $sql Sql command
     * @param boolean $return return result
     * 
     */
    public function execute($sql, $return = false)
    {
        $return = $this->conn->query(Main::Chars($sql));
        if (!empty($this->conn->error)) {
            $this->catchError($this->conn->error, debug_backtrace());
        }
        if (!empty($return)) {
            return $return;
        }
    }

    /**
     * Check database connection
     * 
     * @param string $address  Address
     * @param int    $port     Port
     * @param string $username Username
     * @param string $password Password
     * @param string $database Database
     * 
     */
    public function checkConnection($address, $port, $username, $password, $database)
    {

        $conn = new mysqli($address . ":" . $port, $username, $password);
        $conn->query("USE {$database};");
        if (!empty($conn->error)) {
            echo $conn->error;
            $this->error = $this->errorConvert($conn->error);
            return false;
        }
        $conn->set_charset("utf8mb4");
        if (isset($conn->connect_error)) {
            $this->error = $this->errorConvert($conn->connect_error);
            return false;
        }
        return true;
    }

    /**
     * Add table prefix to table
     * 
     * @param string $table Table
     * 
     */
    public function convertTableName($table)
    {
        if (strpos($table, $this->config->getConfig("Database/prefix")) === false) {
            return $this->config->getConfig("Database/prefix") . $table;
        } else {
            $this->catchError("Converted table, contains table prefix.", debug_backtrace());
        }
    }

    protected function num_rows($rv)
    {
        return $rv->num_rows;
    }
}

