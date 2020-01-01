<?php

use patrick115\Sinusbot\Config;
use patrick115\Sinusbot\Main;

include "../../../src/Class.php";

if (empty($_POST)) {
    Main::Redirect("./?edit&error=Post is empty");
}

if (empty($_POST["debug"])) {
    Main::Redirect("./?edit&error=Please fill form");
}

$bool = Main::Chars($_POST["debug"]);

if ($bool != "true" && $bool != "false") {
    Main::Redirect("./?edit&error=Debug must be boolean (true|false)");
}

$config = file_get_contents(MainDir . "/src/config/config.php");

foreach ($_POST as $name => $value)
{
    $post[$name] = Main::Chars($value);
}

$debug = Main::booltostring(Config::init()->getConfig("debug"));



$config = str_replace(
    [
        "\"debug\" => {$debug},",
    ],
    [
        "\"debug\" => {$post["debug"]},",
    ],
    $config
);

unlink(MainDir . "/src/config/config.php");
$file = fopen(MainDir . "/src/config/config.php", "w");
fwrite($file, $config);
fclose($file);

if(extension_loaded("Zend OPcache")){
    opcache_invalidate(MainDir . "/src/config/config.php", true);
}

$errors->returnError();

Main::Redirect("./");