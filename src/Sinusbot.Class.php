<?php

namespace patrick115\Sinusbot;

use patrick115\Sinusbot\Error;

class Sinusbot extends Error
{
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

}
