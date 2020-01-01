<?php

use patrick115\Sinusbot\Config;
use patrick115\Sinusbot\Main;

include "../../../src/Class.php";

if (empty($_POST)) {
    Main::Redirect("./?edit&error=Post is empty");
}

if (empty($_POST["d_port"]) || empty($_POST["folder"]) || empty($_POST["usedp"]) || empty($_POST["dpassword"])) {
    Main::Redirect("./?edit&error=Please fill form");
}

$bool = Main::Chars($_POST["usedp"]);

if ($bool != "true" && $bool != "false") {
    Main::Redirect("./?edit&error=Use Default Password must be boolean (true|false)");
}

if (!is_numeric($_POST["d_port"])) {
    Main::Redirect("./?edit&error=Default port must be numeric");
}

$config = file_get_contents(MainDir . "/src/config/config.php");

$bot = Config::init()->getConfig("Bot");

if (Main::Chars($_POST["dpassword"]) === Main::Chars(Main::hide($bot["dpassword"]))) {
    $post["dpassword"] = $bot["dpassword"];
    unset($_POST["dpassword"]);
}

foreach ($_POST as $name => $value)
{
    $post[$name] = Main::Chars($value);
}

$config = str_replace(
    [
        "\"d_port\" => {$bot["d_port"]},",
        "\"folder\" => \"{$bot["folder"]}\",",
        "\"usedp\" => " . Main::booltostring($bot["usedp"]) . ",",
        "\"dpassword\" => \"{$bot["dpassword"]}\",",
    ],
    [
        "\"d_port\" => {$post["d_port"]},",
        "\"folder\" => \"{$post["folder"]}\",",
        "\"usedp\" => {$post["usedp"]},",
        "\"dpassword\" => \"{$post["dpassword"]}\",",
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