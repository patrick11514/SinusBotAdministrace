<?php

namespace patrick115\Sinusbot;

use patrick115\Sinusbot\Database;
use patrick115\Sinusbot\Config;

class Main extends Database
{

    private static $ssh = NULL;

    public function __construct()
    {
        Database::Connect();
    }

    public static function Redirect($url)
    {
        header("location: " . $url);
        die();
        exit();
    }

    public static function Chars($text)
    {
        $text = htmlspecialchars($text);
        
        return $text;
    }

    private static function SSHConnect()
    {
        if (self::$ssh === NULL){

            $connection = ssh2_connect(Config::getConfig("SSH/address"), 22);

            ssh2_auth_password($connection, Config::getConfig("SSH/username"), Config::getConfig("SSH/password"));

            self::$ssh = $connection;
        }
        return self::$ssh;
    }

    public static function SSHExecute($command)
    {
        self::SSHConnect();

        $conn = self::$ssh;

        ssh2_exec($conn, $command);
    }

    public function Login($username, $password)
    {

    }
}