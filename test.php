<?php
use patrick115\Sinusbot\Install;
use patrick115\Sinusbot\Database;

$installer = true;

include __DIR__ . "/src/Class.php";

Database::init()->updateConfig();

?>
