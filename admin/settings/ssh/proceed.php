<?php

use patrick115\Sinusbot\Config;
use patrick115\Sinusbot\Main;

include "../../../src/Class.php";

if (empty($_POST)) {
    Main::Redirect("./?edit&error=Post is empty");
}

if (empty($_POST["host"]) || empty($_POST["username"]) || empty($_POST["password"])) {
    Main::Redirect("./?edit&error=Please fill form");
}

$config = file_get_contents(MainDir . "/src/config/config.php");

$ssh = Config::init()->getConfig("SSH");

if (Main::Chars($_POST["password"]) === Main::Chars(Main::hide($ssh["password"]))) {
    $post["password"] = $ssh["password"];
    unset($_POST["password"]);
}

foreach ($_POST as $name => $value)
{
    $post[$name] = Main::Chars($value);
}

$config = str_replace(
    [
        "\"sshaddress\" => \"{$ssh["sshaddress"]}\",",
        "\"sshusername\" => \"{$ssh["sshusername"]}\",",
        "\"sshpassword\" => \"{$ssh["sshpassword"]}\",",
    ],
    [
        "\"sshaddress\" => \"{$post["host"]}\",",
        "\"sshusername\" => \"{$post["username"]}\",",
        "\"sshpassword\" => \"{$post["password"]}\",",
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