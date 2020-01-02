<?php

/**
 * Main class for sinusbot
 * 
 * @author    patrick115 <info@patrick115.eu>
 * @copyright ©2019
 * @link      https://patrick115.eu
 * @link      https://github.com/patrick11514
 * @version   0.1.0
 * 
 */

namespace patrick115\Sinusbot;

use patrick115\Sinusbot\Singleton;

class Error
{

    use Singleton;

    /**
     * Contains all errors
     * 
     * @var array
     */
    private $catcherror = NULL;

    /**
     * Contains error time
     * 
     * @var array
     */
    private $catchtime = NULL;

    private $comm_error;

    private function __construct() {

    }

    /**
     * Catch error
     * 
     * @param string $e Error message
     * @param object $dump Informations about error
     * 
     */
    protected function catchError($e, $dump)
    {   
        $file = $dump[0]["file"];
        $class = $dump[0]["class"];
        $function = $dump[0]["function"];
        $line = $dump[0]["line"];
        $this->catcherror[] = "<b>{$file}({$line}):</b> <i>{$class}::{$function}:</i> " . $e;
        $this->catchtime[] = date("H:i:s");
    }

    /**
     * Return all errors
     * 
     */
    public function returnError()
    {
        if($this->catcherror !== NULL) {
            $return = "<pre id=\"error-list\">";
            $return .= "<b>Errors (" . count($this->catcherror) . "):</b>" . PHP_EOL;
            foreach ($this->catcherror as $id => $error) {
                $return .= "[" . $this->catchtime[$id] . "] " . $error . PHP_EOL;
            }
            $return .= "</pre>";
            ob_end_clean();
            echo $return;
            die();
        }
    }

    protected function putError($e)
    {
        $this->comm_error = $e;
    }

    public function getError()
    {
        return $this->comm_error;
    }
}