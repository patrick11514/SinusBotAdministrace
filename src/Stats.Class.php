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

use patrick115\Sinusbot\Singleton;

class Stats extends Database
{
    use Singleton;

    private $database;

    private function __construct()
    {
        $this->database = Database::init();
    }

    public function getRegistredUsers()
    {
        return $this->database->getCountRows("users");
    }

    public function getBots()
    {
        return $this->database->getCountRows("bots");
    }
}