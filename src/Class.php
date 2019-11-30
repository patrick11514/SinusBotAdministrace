<?php

spl_autoload_register(function($class) {
    $first = __DIR__ . '/';
    $extension = '.Class.php';
    $class = explode('\\', $class);
    $path = $first . end($class) . $extension;

    include_once $path;
});