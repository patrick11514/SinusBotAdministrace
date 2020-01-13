<?php

use patrick115\Sinusbot\Database;
use patrick115\Sinusbot\Main;

include "../src/Class.php";

if (empty($_POST) || empty($_POST["username"]) || empty($_POST["password"]) || empty($_POST["2password"]) || empty($_POST["key"])) {
    Main::Redirect("./?key={$_POST["key"]}&e=Please fill form");
}

$db = Database::init();
$main = new Main();

$rv = $db->select(["key"], "invites", "LIMIT 1", "key", Main::Chars($_POST["key"]));

if ($db->num_rows($rv) == 0) {
    die("Key not found!");
}

$password = Main::Chars($_POST["password"]);
$ndpassword = Main::Chars($_POST["2password"]);
$username = Main::Chars($_POST["username"]);
$key = Main::Chars($_POST["key"]);



if ($password !== $ndpassword) {
    Main::Redirect("./?key={$key}&e=Passwords don't match.");
}

$ret = $db->select(["id"], "users", "LIMIT 1", "username", $username);

if ($db->num_rows($rv) == 0) {
    $db->delete("invites", ["key"], [$key]);
    die("This invitation for $username has been already claimed.");
}

$db->delete("invites", ["key"], [$key]);

$main->createUser($username, $password, Main::getUserIP());

Main::Redirect("../");