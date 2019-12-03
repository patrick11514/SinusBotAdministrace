<?php

namespace patrick115\Sinusbot;

use patrick115\Sinusbot\Config;
use patrick115\Sinusbot\Main;

class Sinusbot 
{
    public static function generateConfig(int $port, $folder)
    {
        $config = "ListenPort = {$port}".PHP_EOL."ListenHost = \"0.0.0.0\"".PHP_EOL."TS3Path = \"{$folder}/TeamSpeak3-Client-linux_amd64/ts3client_linux_amd64\"";
        
        if (!is_writable($folder)) {
            die("<b>Create Config Error:</b><br><i>While creating config:</i> Folder is not writable!");
        }

        $file = fopen($folder . "/config.ini", "w");
        fwrite($file, $config);
        fclose($file);
    }

}
