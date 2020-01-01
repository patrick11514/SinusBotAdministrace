<?php

use patrick115\Sinusbot\Main;
use patrick115\Sinusbot\Session;

include "../../src/Class.php";

Session::destroy();

Main::Redirect("../");