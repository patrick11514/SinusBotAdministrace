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

    public function getUserList()
    {
        $rv = $this->database->select(["id", "username", "last_ip"], "users");
        if ($this->database->num_rows($rv) > 0) {
            $return = "<table class=\"table table-hover\">";
            $return .= "<thead>
            <tr>
                <th style=\"text-align:center;\">Id</th>
                <th style=\"text-align:center;\">Username</th>
                <th style=\"text-align:center;\">Last IP</th>
                <th style=\"max-width:30%;width:20%;\"></th>
            <tr>
            </thead>
            <tbody>";
            while ($row = $rv->fetch_assoc()) {
                $return .= "<tr>
                <td style=\"text-align:center;\">{$row["id"]}</td>
                <td style=\"text-align:center;\">{$row["username"]}</td>
                <td style=\"text-align:center;\">{$row["last_ip"]}</td>
                <td style=\"text-align:center;\">
                    <a href=\"../profile?u={$row["username"]}\">
                        <button type=\"button\" class=\"btn btn-block btn-info\">Profile</button>
                    </a>
                </td>
            </tr>";
            }
            $return .= "</tbody></table>";
            return $return;
                
        } else {
            return "<h2 style=\"color:red\">No users found, please create one.</h2>";
        }
    }
}
