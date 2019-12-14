<?php

session_start();

ini_set('zlib.output_compression', 0);
ini_set('implicit_flush', 1);
ob_end_clean();
set_time_limit(0);
ob_implicit_flush(1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

spl_autoload_register(function($class) {
    $first = __DIR__ . '/';
    $extension = '.Class.php';
    $class = explode('\\', $class);
    $path = $first . end($class) . $extension;

    include_once $path;
});