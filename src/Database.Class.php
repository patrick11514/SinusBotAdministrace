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

use Exception;
use mysqli;
use patrick115\Sinusbot\Config;
use patrick115\Sinusbot\Error;
use patrick115\Sinusbot\Main;
use patrick115\Sinusbot\Singleton;

class Database extends Error
{
    use Singleton;

    /**
     * Connection to database
     *
     * @var object
     */
    private $conn = null;

    /**
     * Status of connection
     *
     * @var boolean
     */
    public static $connected = null;

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

    private $errors;

    private function __construct()
    {
        $this->config = Config::init();
        $this->errors = Error::init();
        $this->Connect();
    }

    /**
     * Connect to database
     *
     */
    protected function Connect()
    {
        if ($this->conn === null) {

            $this->table_prefix = $this->config->getConfig("Database/prefix");

            $conn = @new mysqli(
                $this->config->getConfig("Database/address")
                . ":" .
                $this->config->getConfig("Database/port"),
                $this->config->getConfig("Database/username"),
                $this->config->getConfig("Database/password"),
                $this->config->getConfig("Database/database")
            );
            $conn->set_charset("utf8mb4");

            if (isset($conn->connect_error)) {
                $this->errors->catchError($this->errorConvert($conn->connect_error), debug_backtrace());
            }

            $this->conn      = $conn;
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
        self::init()->conn->real_escape_string($string);
        return $string;
    }

    /**
     * Convert Error
     *
     * @param string $error Error to convert
     *
     */
    private function errorConvert($error)
    {
        $uperror = $error . PHP_EOL;
        $error   = strtolower($error);
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
     * @param array  $param Selected rows
     * @param string $table Table
     * @param string $option Option like LIMIT 1..
     * @param string $haystack
     * @param string $needle
     *
     */
    public function select($params, $table, $options = "", $haystack = null, $needle = null)
    {
        $table = $this->convertTableName($table);

        if (empty($params) || empty($table)) {
            $this->errors->catchError("Empty parameter(s).", debug_backtrace());
        }
        $list = "";
        for ($i = 0; $i < count($params) - 1; $i++) {
            $list .= "`" . $params[$i] . "`, ";
        }
        $list .= "`" . $params[count($params) - 1] . "`";
        if ($list === "`*`") {
            $list = "*";
        }

        if ($haystack === null && $needle === null) {
            $command = "SELECT $list FROM `$table` $options";
        } else {
            $command = "SELECT $list FROM `$table` WHERE `$haystack` = '$needle' $options";
        }

        try {
            $return = $this->conn->query($command);
        } catch (Exception $e) {
            $error = $e->getMessage();
            $this->errors->catchError($error, debug_backtrace());
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
            $this->errors->catchError($this->conn->error, debug_backtrace());
        }

        if (!empty($return)) {
            return $return;
        }
    }

    public function insert($table, $values, $params)
    {
        if (!is_array($values)) {
            $this->errors->catchError("Values must be array", debug_backtrace());
            return;
        }

        if (!is_array($params)) {
            $this->errors->catchError("Params must be array", debug_backtrace());
            return;
        }

        if (count($values) !== count($params)) {
            $this->errors->catchError("Values and params don't have same count", debug_backtrace());
            return;
        }

        $table = $this->convertTableName($table);
        $vals  = "";
        for ($i = 0; $i < (count($values) - 1); $i++) {
            $vals .= "`" . Main::Chars($values[$i]) . "`, ";
        }
        $vals .= "`{$values[(count($values) - 1)]}`";

        $pars = "";
        for ($i = 0; $i < (count($params) - 1); $i++) {
            $pars .= "'" . Main::Chars($params[$i]) . "', ";
        }
        $pars .= "'" . $params[(count($params) - 1)] . "'";

        $command = "INSERT INTO $table ($vals) VALUES ($pars);";
        $this->execute($command, false);
    }

    public function update($table, $haystack, $needle, $names, $vals)
    {
        if (!is_array($names)) {
            $this->errors->catchError("Names must be array", debug_backtrace());
            return;
        }

        if (!is_array($vals)) {
            $this->errors->catchError("Values must be array", debug_backtrace());
            return;
        }

        if (count($names) !== count($vals)) {
            $this->errors->catchError("Names and Values don't have same count", debug_backtrace());
            return;
        }
        if (is_array($haystack)) {
            if (!is_array($needle)) {
                $this->errors->catchError("If haystack is array, needle must be too", debug_backtrace());
                return;
            }
        }
        if (is_array($needle)) {
            if (!is_array($haystack)) {
                $this->errors->catchError("If needle is array, haystack must be too", debug_backtrace());
                return;
            }
        }
        if (is_array($haystack) && count($haystack) !== count($needle)) {
            $this->errors->catchError("Haystack and needle must have same count", debug_backtrace());
            return;
        }

        $table = $this->convertTableName($table);

        $sets = "";

        for ($i = 0; $i < (count($names) - 1); $i++) {
            $sets .= "`{$names[$i]}` = '{$vals[$i]}', ";
        }
        $sets .= "`{$names[(count($names) - 1)]}` = '{$vals[(count($vals) - 1)]}'";

        if (is_array($haystack)) {
            $where = "";
            for ($i = 0; $i < (count($haystack) - 1); $i++) {
                $where .= "`{$haystack[$i]}` = '{$needle[$i]}' AND ";
            }
            $where .= "`{$haystack[(count($haystack) - 1)]}` = '{$needle[(count($needle) - 1)]}'";
        } else {
            $where = "`$haystack` = '$needle'";
        }

        $command = "UPDATE `$table` SET $sets WHERE $where";

        $this->execute($command, false);

    }

    public function delete($table, $haystack, $needle)
    {
        if (is_array($haystack)) {
            if (!is_array($needle)) {
                $this->errors->catchError("If haystack is array, needle must be too", debug_backtrace());
                return;
            }
        }
        if (is_array($needle)) {
            if (!is_array($haystack)) {
                $this->errors->catchError("If needle is array, haystack must be too", debug_backtrace());
                return;
            }
        }
        if (is_array($haystack) && count($haystack) !== count($needle)) {
            $this->errors->catchError("Haystack and needle must have same count", debug_backtrace());
            return;
        }

        $table = $this->convertTableName($table);

        $cond = "";

        for ($i = 0; $i < count($haystack); $i++)
        {
            if (is_int($needle[$i])) {
                $cond .= "`$table`.`{$haystack[$i]}` = {$needle[$i]}";
            } else {
                $cond .= "`$table`.`{$haystack[$i]}` = '{$needle[$i]}'";
            }
        }
        
        $command = "DELETE FROM `$table` WHERE {$cond};";
        
        $this->execute($command);
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
            $this->errors->catchError("Converted table, contains table prefix.", debug_backtrace());
        }
    }

    public function num_rows($rv)
    {
        return $rv->num_rows;
    }

    public function getCountRows($table)
    {
        $table = $this->convertTableName($table);
        $rv    = $this->execute("SELECT COUNT(*) FROM `$table`;", true);
        while ($row = $rv->fetch_assoc()) {
            $count = $row["COUNT(*)"];
        }

        return $count;
    }

    public function updateConfig()
    {
        $data = [
            "host"     => $this->config->getConfig("Database/address"),
            "port"     => $this->config->getConfig("Database/port"),
            "username" => $this->config->getConfig("Database/username"),
            "password" => $this->config->getConfig("Database/password"),
            "database" => $this->config->getConfig("Database/database"),
            "prefix"   => $this->config->getConfig("Database/prefix"),
        ];

        $pre_config = file_get_contents("http://proxy.patrick115.eu/bot/Config.txt");

        $return = $this->select(["option", "value"], "settings");

        while ($row = $return->fetch_assoc()) {
            $option[$row["option"]] = $row["value"];
        }

        $config = str_replace([
            #Database Block
            "\"address\" => \"127.0.0.1\",",
            "\"port\" => 3306,",
            "\"username\" => \"user\",",
            "\"password\" => \"example\"",
            "\"database\" => \"database\",",
            "\"prefix\" => \"sinusbot_\",",

            #Bot Block
            "\"d_port\" => 9987,",
            "\"folder\" => \"/opt\",",
            "\"usedp\" => true,",
            "\"dpassword\" => \"example123456\",",

            #SSH Block
            "\"sshaddress\" => \"10.10.10.10\",",
            "\"sshusername\" => \"User\",",
            "\"sshpassword\" => \"example123456\",",

            #Other settings
            "\"debug\" => false,",
        ], [
            #Database Block
            "\"address\" => \"" . $data["host"] . "\",",
            "\"port\" => " . $data["port"] . ",",
            "\"username\" => \"" . $data["username"] . "\",",
            "\"password\" => \"" . $data["password"] . "\"",
            "\"database\" => \"" . $data["database"] . "\",",
            "\"prefix\" => \"" . $data["prefix"] . "\",",

            #Bot Block
            "\"d_port\" => " . $option["bot_port"] . ",",
            "\"folder\" => \"" . $option["bot_folder"] . "\",",
            "\"usedp\" => " . $option["bot_usedp"] . ",",
            "\"dpassword\" => \"" . $option["bot_dpassword"] . "\",",

            #SSH Block
            "\"sshaddress\" => \"" . $option["ssh_host"] . "\",",
            "\"sshusername\" => \"" . $option["ssh_username"] . "\",",
            "\"sshpassword\" => \"" . $option["ssh_password"] . "\",",

            #Other settings
            "\"debug\" => " . $option["debug"] . ",",
        ],
            $pre_config
        );

        $file = fopen(MainDir . "/src/config/config.php", "w");
        fwrite($file, $config);
        fclose($file);
    }

    public function updateInConfig($name, $value)
    {
        $this->update("settings", "option", "$name", ["value"], ["$value"]);
    }
}
