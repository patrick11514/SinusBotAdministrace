<?php

use patrick115\Sinusbot\Config;
use patrick115\Sinusbot\Main;
use patrick115\Sinusbot\Database;

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

if (Main::Chars($_POST["dpassword"]) === Main::Chars(Main::hide(Config::init()->getConfig("Bot/dpassword")))) {
    $post["dpassword"] = Config::init()->getConfig("Bot/dpassword");
    unset($_POST["dpassword"]);
}

foreach ($_POST as $name => $value)
{
    $post[$name] = Main::Chars($value);
}

$db = Database::init();

$db->updateInConfig("bot_port", $post["d_port"]);
$db->updateInConfig("bot_folder", $post["folder"]);
$db->updateInConfig("bot_usedp", $post["usedp"]);
$db->updateInConfig("bot_dpassword", $post["dpassword"]);

$db->updateConfig();

if(extension_loaded("Zend OPcache")){
    opcache_invalidate(MainDir . "/src/config/config.php", true);
}

$errors->returnError();

Main::Redirect("./");