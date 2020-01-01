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

use patrick115\Sinusbot\Database;
use patrick115\Sinusbot\Config;
use patrick115\Sinusbot\Error;

class Main extends Database
{

    /**
     * Current Version
     * 
     * @var string
     */
    private static $version = "0.1.0";

    /**
     * Current build
     * 
     * @var string
     */
    private static $build = "beta";

    /**
     * SSH connection
     * 
     * @var object
     */
    private static $ssh = NULL;
    
    private $config;

    private $error_c;

    private $database_c;

    /**
     * On construct connect to database
     * 
     */
    public function __construct()
    {
        $this->database_c = Database::init();
        $this->config = Config::init();
        $this->error_c = Error::init();
    }

    /**
     * Safe redirect by header
     * 
     * @param string $url Url
     * 
     */
    public static function Redirect($url)
    {
        header("location: " . $url);
        die();
        exit();
    }

    /**
     * Validate form inputs from 1st part
     * 
     * @param boolean $version Return version or no
     * @param boolean $build Return build or no
     * 
     */
    public static function getVerBuild($version = true, $build = false)
    {

        if ($version === true) {
            $version = " " . self::$version;
        } else {
            $version = "";
        }
        if ($build === true) {
            $build = self::$build;
        } else {
            $build = "";
        }
        return $build . $version;
    }

    /**
     * Connect to SSH
     * 
     */
    private function SSHConnect()
    {
        if (self::$ssh === NULL){
            self::$ssh = "-";
            if (empty($this->config->getConfig("SSH/sshaddress")) || $this->config->getConfig("SSH/sshaddress") === "") {
                parent::catchError("Connection failded to " . $this->config->getConfig("SSH/shhaddress"), debug_backtrace());
            } else {
                $connection = ssh2_connect($this->config->getConfig("SSH/sshaddress"), 22);
                if (!ssh2_auth_password($connection, $this->config->getConfig("SSH/sshusername"), $this->config->getConfig("SSH/sshpassword"))) {
                    parent::catchError("Login credentials is invalid", debug_backtrace());
                } else {
                    self::$ssh = $connection;
                }
            }
            
        }
        return self::$ssh;
    }

    /**
     * Execute SSH command
     * 
     * @param string $command Command to execute
     * 
     */
    public function SSHExecute($command)
    {
        $this->SSHConnect();

        $conn = self::$ssh;

        ssh2_exec($conn, $command);
    }

    /**
     * Converts text and html tags to text
     * 
     * @param string $text Cenverted text
     * 
     */
    public static function Chars($text)
    {
        if (parent::$connected === true) {
            $text = parent::removeChars($text);
        }

        $text = htmlspecialchars($text);
        
        return $text;
    }

    /**
     * Destroy session
     * 
     * @param boolean $delete Delete session?
     * 
     */
    public static function sessDestroy($delete = true)
    {
        session_destroy();
        session_write_close();
        setcookie(session_name(), '', 0, '/');
        session_regenerate_id($delete);
    }

    /**
     * Create user
     * 
     * @param string $username  Username
     * @param string $password  Password
     * @param string $ipaddress IpAddress
     * @param string $perms     Permissions
     * 
     */
    public function createUser($username, $password, $ipaddress, $perms = "default")
    {
        if (Database::init()->select(["username"], "users", "LIMIT 1", "username", $username)->num_rows <= 0) {
            Database::init()->insert("users", ["id", "username", "password", "register_ip", "last_ip", "bot_id"], ["", $username, password_hash($password, PASSWORD_BCRYPT, array("cost" => 10)), $ipaddress, $ipaddress, ""]);
        } else {
            $this->catchError("User {$username} Already Exist", debug_backtrace());
        }
    }

    /**
     * Get user ip
     * 
     */
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

    public function validateCredentials($username, $password)
    {
        $return = $this->database->execute("SELECT `password`, `last_ip` FROM `" . $this->database->convertTableName("users") . "` WHERE `username` = '$username'", true);
        $obj = $return->fetch_object();
    
        if (!empty($this->database->num_rows($return)) && $this->database->num_rows($return) > 0) {
            if (password_verify($password, $obj->password)) {
                if ($obj->last_ip == $this->getUserIP()) {
                    return true;
                } else {
                    $this->database->update("users", "username", $username, ["last_ip"], [$this->getUserIP()]);
                    ## Email Notify
                    return true;
                }
            } else {
                $this->error_c->putError("Password doesn't match");
                return false;
            }
        } else {
            $this->error_c->putError("Username not found");
            return false;
        }
        
    }

    public function Login($username, $password)
    {
        $username = self::Chars($username);
        $password = self::Chars($password);
        if (!$this->validateCredentials($username, $password)) {
            return $this->getError();
        }
        session_regenerate_id(true);
        Session::put(["logged", "username"], [true, $username]);
        if (isset($_GET["back"])) {
            $url = "../" . $this->Chars($_GET["back"]);
        } else {
            $url = "../";
        }
        $this->Redirect($url);
    }

    public static function hide($string)
    {
        $count = strlen($string);
        $return = "";
        for ($i = 0; $i < $count; $i++)
        {
            $return .= "*";
        }
        return $return;
    }

    public static function booltostring($bool)
    {
        if ($bool === true) {
            return "true";
        }
        return "false";
    }

    public function getUserByID($id)
    {
        $username = Database::init()->select(["username"], "users", "LIMIT 1", "id", $id);
        if (Database::init()->num_rows($username) > 0) {
            return $username->fetch_object()->username;
        } else {
            Error::init()->catchError("User with id $id not found", debug_backtrace());
            return;
        }
    }
}
