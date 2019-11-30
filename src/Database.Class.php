<?php

namespace patrick115\Sinusbot;

use mysqli;

class Database
{

    private static $conn = NULL;

    public static $error;

    protected static function Connect()
    {
        if (self::$conn === NULL) {
            include __DIR__ . "/config/config.php";

            $config = $cfg["database"];

            $conn = new mysqli($config["address"] . ":" . $config["port"], $config["username"], $config["password"]);
            $conn->set_charset("utf8mb4");

            if( isset($conn->connect_error)) {
                die("<b>MYSQLI Error:</b>" . self::errorConvert($conn->connect_error));
            }

            self::$conn = $conn;
        }
    }

    private static function errorConvert($error)
    {
        if ($error == "php_network_getaddresses: getaddrinfo failed: No address associated with hostname") {
            $return = "Undefined host.";
        } else if (strpos($error, "Access denied for user ") !== false) {
            $return = "Incorrect Login Data";
        } else if ($error == "php_network_getaddresses: getaddrinfo failed: No address associated with hostname") {
            $return = "Please use valid host";
        } else if ($error == "php_network_getaddresses: getaddrinfo failed: Name or service not known") {
            $return = "Please use valid host";
        }
        return $return;
    }

    public static function checkConnection($address, $port, $username, $password)
    {

        $conn = new mysqli($address . ":" . $port, $username, $password);
        
        $conn->set_charset("utf8mb4");

        if (isset($conn->connect_error)) {
            self::$error = self::errorConvert($conn->connect_error);
            return false;
        }
        return true;
    }
}
