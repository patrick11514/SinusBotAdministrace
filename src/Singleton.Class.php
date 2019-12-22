<?php

namespace patrick115\Sinusbot;

trait Singleton
{
    private static $instance = NULL;

    public static function init()
    {
        if (self::$instance === NULL) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}



