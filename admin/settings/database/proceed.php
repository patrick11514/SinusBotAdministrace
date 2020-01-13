<?php

use patrick115\Sinusbot\Config;
use patrick115\Sinusbot\Main;
use patrick115\Sinusbot\Session;

include "../../../src/Class.php";

if (empty(Session::get("logged")))
{
    header("HTTP/1.0 404 Not Found");
    exit;
}

if (empty($_POST)) {
    Main::Redirect("./?edit&error=Post is empty");
}

if (empty($_POST["host"]) || empty($_POST["username"]) || empty($_POST["password"]) || empty($_POST["database"]) || empty($_POST["port"]) ||empty($_POST["prefix"])) {
    Main::Redirect("./?edit&error=Please fill form");
}

$config = file_get_contents(MainDir . "/src/config/config.php");

$db = Config::init()->getConfig("Database");

if (Main::Chars($_POST["password"]) === Main::Chars(Main::hide($db["password"]))) {
    $post["password"] = $db["password"];
    unset($_POST["password"]);
}

foreach ($_POST as $name => $value)
{
    $post[$name] = Main::Chars($value);
}

$config = str_replace(
    [
        "\"address\" => \"{$db["address"]}\",",
        "\"port\" => {$db["port"]},",
        "\"username\" => \"{$db["username"]}\",",
        "\"password\" => \"{$db["password"]}\",",
        "\"database\" => \"{$db["database"]}\",",
        "\"prefix\" => \"{$db["prefix"]}\","
    ],
    [
        "\"address\" => \"{$post["host"]}\",",
        "\"port\" => {$post["port"]},",
        "\"username\" => \"{$post["username"]}\",",
        "\"password\" => \"{$post["password"]}\",",
        "\"database\" => \"{$post["database"]}\",",
        "\"prefix\" => \"{$post["prefix"]}\","
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