<?php

use patrick115\Sinusbot\Config;
use patrick115\Sinusbot\Error;
if (empty(@constant('MainDir'))) {
    define("MainDir", __DIR__ . "/..");
}

ini_set('zlib.output_compression', 0);
ini_set('implicit_flush', 1);
ini_set('output_buffering', 'Off');
ini_set('output_handler', '');
ini_set('implicit_flush', 'On');
ob_implicit_flush(1);
ob_end_clean();

header('Content-Encoding: none');
header("Cache-Control: no-cache, must-revalidate");
header('X-Accel-Buffering: no');

if (empty($installer)) {
    ob_start();
}

spl_autoload_register(function ($class) {
    $first     = __DIR__ . '/';
    $extension = '.Class.php';
    $class     = explode('\\', $class);
    $path      = $first . end($class) . $extension;

    include_once $path;
});

session_start();

$config = Config::init();

if (empty($installer) && !file_exists(MainDir . "/src/installer/install.lock")) {
    include MainDir . "/src/installer/empty.php";
    exit;
}

if (Config::existConfig()) {
    if ($config->getConfig("debug")) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        if (empty($installer) || $installer !== true) {
            echo "<center><h4 style=\"color:red\">Debug mode enabled!</h4></center>";
        }
    }
}

$errors = Error::init();
