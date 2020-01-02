<?php

use patrick115\Sinusbot\Config;
use patrick115\Sinusbot\Main;
use patrick115\Sinusbot\Database;

include "../../../src/Class.php";

if (empty($_POST)) {
    Main::Redirect("./?edit&error=Post is empty");
}

if (empty($_POST["host"]) || empty($_POST["username"]) || empty($_POST["password"])) {
    Main::Redirect("./?edit&error=Please fill form");
}

$config = file_get_contents(MainDir . "/src/config/config.php");

$ssh = Config::init()->getConfig("SSH");

if (Main::Chars($_POST["password"]) === Main::hide(Main::Chars($ssh["sshpassword"]))) {
    $post["password"] = $ssh["sshpassword"];
    unset($_POST["password"]);
}

foreach ($_POST as $name => $value)
{
    $post[$name] = Main::Chars($value);
}

$db = Database::init();

$db->updateInConfig("ssh_host", $post["host"]);
$db->updateInConfig("ssh_username", $post["username"]);
$db->updateInConfig("ssh_password", $post["password"]);

$db->updateConfig();

if(extension_loaded("Zend OPcache")){
    opcache_invalidate(MainDir . "/src/config/config.php", true);
}

$errors->returnError();

Main::Redirect("./");