<?php

namespace patrick115\Sinusbot;

use mysqli;

class Install extends Database
{

    public static $lasterror;

    public static function validate_1($arr)
    {
        if (empty($arr["address"]) || empty($arr["port"]) || empty($arr["username"] || empty($arr["password"]))) {
            self::$lasterror = "Please fill form";
            return false;
        } else if (!is_numeric($arr["port"])) {
            self::$lasterror = "Port must be numeric";
            return false;
        }
        return true;
    }

    public static function validate_2($arr)
    {
        if (empty($arr["D_Port"]) || empty($arr["Folder"]) || empty($arr["UseDP"])) {
            self::$lasterror = "Please fill form";
            return false;
        } else if (!is_numeric($arr["D_Port"])) {
            self::$lasterror = "Port must be numeric";
            return false;
        } else if ($arr["UseDP"] != "true" && $arr["UseDP"] != "false") {
            self::$lasterror = "Use default port value must be True/False";
            return false;
        } else if ($arr["UseDP"] == "true" && empty($arr["DPassword"])) {
            self::$lasterror = "If you can use default password, you must define it";
            return false;
        }
        return true;
    }

    public static function validate_3($arr)
    {
        if (empty($arr["address"]) || empty($arr["username"]) || empty($arr["password"])) {
            self::$lasterror = "Please fill form";
            return false;
        }
        return true;
    }

    public static function randomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function checkVersion()
    {
        $errors = array();
        if (version_compare(phpversion(), '5.4.0', '<')) {
            $errors[] = "Please use PHP 5.4.0+";
        }
        if (!extension_loaded("mysqli")) {
            $errors[] = "Please install mysqli extension";
        }
        if (!extension_loaded("curl")) {
            $errors[] = "Please install curl extension";
        }
        if (!extension_loaded("gd")) {
            $errors[] = "Please install gd extension";
        }

        if (!empty($errors)) {
            $return = "<pre>There was an error(s) checking the extensions and php version<ul>";
            foreach ($errors as $error) {
                $return .= "<li>{$error}</li>";
            }
            $return .= "</ul></pre>";
            die($return);
        }
    }

}