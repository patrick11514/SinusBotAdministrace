<?php

function generateRandomString($length = 10)
{
    $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

use patrick115\Sinusbot\Database;
use patrick115\Sinusbot\Main;
use patrick115\Sinusbot\Session;

include "../../../src/Class.php";

if (empty(Session::get("logged")))
{
    header("HTTP/1.0 404 Not Found");
    exit;
}

if (empty($_POST) || empty($_POST["username"])) {
    Main::Redirect("./?e=Please fill form");
}

$main = new Main();
$db  = Database::init();
/**
 * Prepare
 */

$creator = $main->getIDByUser(Session::get("username"));
$for     = Main::Chars($_POST["username"]) ?? null;
$message = Main::Chars($_POST["message"]) ?? null;
$string  = generateRandomString(40);

if ($main->userExist($main->getIDByUser($for, false))) {
    Main::Redirect("./?e=User with this name already exist");
} 

$result = $db->select(["id"], "invites", "LIMIT 1", "for", $for);

if ($db->num_rows($result) > 0) {
    Main::Redirect("./?e=Invitation for user $for already exist");
}

$db->insert("invites", ["creator", "for", "message", "key"], [$creator, $for, $message, $string]);

$errors->returnError();

Main::Redirect("./?key=$string&for=$for&message=$message");