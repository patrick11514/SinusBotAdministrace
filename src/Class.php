<?php

namespace patrick115\Sinusbot;

use mysqli;

class Main
{
    private $conn = NULL;

    public function Database()
    {
        if ($this->conn === NULL) {
            include __DIR__ . "/config.php";

            $config = $cfg["database"];

            $conn = new mysqli($config["address"] . ":" . $config["port"], $config["username"], $config["password"]);
            $conn->set_charset(UTF)
        }
    }

    public function Login($username, $password)
    {

    }
}