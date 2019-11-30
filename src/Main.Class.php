<?php

namespace patrick115\Sinusbot;

use patrick115\Sinusbot\Database;

class Main extends Database
{

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

    public function Login($username, $password)
    {

    }
}