<?php

namespace patrick115\Sinusbot;

use Exception;
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
            self::$ssh = "-";
            if (empty(Config::getConfig("SSH/address")) || Config::getConfig("SSH/address") === "") {
                parent::catchError("Connection failded to " . Config::getConfig("SSH/address"), debug_backtrace());
            } else {
                $connection = ssh2_connect(Config::getConfig("SSH/address"), 22);
                if (!ssh2_auth_password($connection, Config::getConfig("SSH/username"), Config::getConfig("SSH/password"))) {
                    parent::catchError("Login credentials is invalid", debug_backtrace());
                } else {
                    self::$ssh = $connection;
                }
            }
            
        }
        return self::$ssh;
    }

    public static function SSHExecute($command)
    {
        self::SSHConnect();

        $conn = self::$ssh;

        ssh2_exec($conn, $command);
    }

    public static function sessDestroy($delete = true)
    {
        session_destroy();
        session_write_close();
        setcookie(session_name(), '', 0, '/');
        session_regenerate_id($delete);
    }

    public function createUser($username, $password, $ipaddress, $perms = "default")
    {
        if (Database::select(["username"], "users", "LIMIT 1", "username", $username)->num_rows <= 0) {
            Database::execute("INSERT INTO `sinusbot_users` (`id`, `username`, `password`, `register_ip`, `last_ip`, `bot_id`) VALUES (NULL, '$username', '$password', '$ipaddress', '$ipaddress', '')");
        } else {
            parent::catchError("User {$username} Already Exist", debug_backtrace());
        }
    }

    public static function getUserIP()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])){
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        }else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else if(isset($_SERVER['HTTP_X_FORWARDED'])){
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        }else if(isset($_SERVER['HTTP_FORWARDED_FOR'])){
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        }else if(isset($_SERVER['HTTP_FORWARDED'])){
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        }else if(isset($_SERVER['REMOTE_ADDR'])){
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        }else{
            $ipaddress = 'UNKNOWN';
        }
    
        return $ipaddress;
    }

    public function Login($username, $password)
    {

    }

    public static function test()
    {
        var_dump(debug_backtrace());
    }
}