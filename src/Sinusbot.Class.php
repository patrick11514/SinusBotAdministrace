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

use patrick115\Sinusbot\Error;
use patrick115\Sinusbot\Singleton;
use patrick115\Sinusbot\Database;

class Sinusbot extends Error
{
    use Singleton;

    private $database;

    private function __construct()
    {
        $this->database = Database::init();
    }

    public static function generateConfig(int $port, $folder)
    {
        $config = "ListenPort = {$port}".PHP_EOL."ListenHost = \"0.0.0.0\"".PHP_EOL."TS3Path = \"{$folder}/TeamSpeak3-Client-linux_amd64/ts3client_linux_amd64\"";
        
        if (!is_writable($folder)) {
            parent::catchError("Folder ($folder) is not writable!", debug_backtrace());
        } else {

            $file = fopen($folder . "/config.ini", "w");
            fwrite($file, $config);
            fclose($file);
        }
    }

    public function getBots()
    {
        $val = $this->database->select(["*"], "bots");
        if ($this->database->num_rows($val) > 0) {
            $return = "<table class=\"table table-hover\">";
            $return .= "
            <thead>
                <tr>
                    <th>id</th>
                    <th>Owner</th>
                    <th>Port</th>
                    <th>Status</th>
                <tr>
            </thead>
            <tbody>
            ";
            while ($row = $val->fetch_assoc()) {
                $return .= "
                <tr>
                    <td>{$row["id"]}</td>
                    <td>" . Main::getUserByID($row["owner"]) . "</td>
                    <td>{$row["port"]}</td>
                    <td>" . $this->convertStatus($row["status"]) . "</td>
                </tr>";
            }
            $return .= "</tbody></table>";
            return $return;
        } else {
            return "<h2 style=\"color:red\">No bots found, create new</h2>";
        }
    }
    private static function convertStatus($status)
    {
        if ($status == "false") {
            return "Offline";
        }
        return "Online";
    }
}
