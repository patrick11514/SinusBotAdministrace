<?php

ini_set('zlib.output_compression', 0);
ini_set('implicit_flush', 1);
ob_end_clean();
set_time_limit(0);
ob_implicit_flush(1);

session_start();

use patrick115\Sinusbot\Database;
use patrick115\Sinusbot\Error;
use patrick115\Sinusbot\Install;
use patrick115\Sinusbot\Main;

include __DIR__ . "/src/Class.php";

include __DIR__ . "/src/installer/installs.php";

if (file_exists(__DIR__ . "/src/installer/install.lock")) {
    Main::Redirect("./index.php");
}

Install::checkVersion();

if (!is_writable(__DIR__)) {
    $error = "<pre>Folder " . __DIR__ . " is not writable!" . PHP_EOL . "Please use these 2 options to repair it:" . PHP_EOL . "<ul><li>Give this folder to user <b>www-data</b> <i>(recommended)</i></li><li>Give this folder 0777 permissions</li></ul>";
    die($error);
}

if (empty($_GET["setup"])) {
        
    if (file_exists(__DIR__ . "/src/config/config.php")) {
        unlink(__DIR__ . "/src/config/config.php");
    }
    
    Main::Redirect("./install.php?setup=1");

}

if (empty($_SESSION["temp"])) {
    $_SESSION["temp"] = Install::randomString(40);
    $_SESSION["data"] = [
        1 => false,
        2 => false,
        3 => false,
        4 => false,
        5 => false,
    ];
    fopen(__DIR__ . "/temp_" . $_SESSION["temp"] . ".txt", "w");
    Main::Redirect("./install.php?setup=1");
} else {
    if (!file_exists(__DIR__ . "/temp_" . $_SESSION["temp"] . ".txt")) {
        session_unset();
        session_regenerate_id(true);
        include __DIR__ . "/src/install/recreate.php";
        Main::Redirect("./install.php?setup=1");
    }
}

$part = Main::Chars($_GET["setup"]);

if ($part > 1 && $_SESSION["data"][($part - 1)] === false) {
    Main::Redirect("./install.php?setup=" . ($part - 1));
}

if (empty($source[$part]) || empty($install[$part])) {
    Main::Redirect("./install.php?setup=1");
}

$check = @file_get_contents("http://proxy.patrick115.eu/ping.txt");
if ($check === false) {
    die("Can't contact proxy site, please contact me on info@patrick115.eu!");
}

if (isset($_POST["submit"])) {
    if ($part == 1) {
        if (Install::validate_1($_POST)) {
            if (Database::checkConnection(
                Main::Chars($_POST["address"]),
                Main::Chars($_POST["port"]),
                Main::Chars($_POST["username"]),
                Main::Chars($_POST["password"]),
                Main::Chars($_POST["database"])
            )) {

                $connection = new mysqli(
                    Main::Chars($_POST["address"]) . ":" . 
                    Main::Chars($_POST["port"]),
                    Main::Chars($_POST["username"]),
                    Main::Chars($_POST["password"]),
                    Main::Chars($_POST["database"]));
                
                $tables = $connection->query("SHOW TABLES;");

                $tbl = false;

                while ($row = $tables->fetch_assoc()) {
                    $tbl = true;
                }

                if ($tbl === false) {

                    $_SESSION["data"]["database"] = [
                        "address" => Main::Chars($_POST["address"]),
                        "port" => Main::Chars($_POST["port"]),
                        "username" => Main::Chars($_POST["username"]),
                        "password" => Main::Chars($_POST["password"]),
                        "database" => Main::Chars($_POST["database"]),
                        "prefix" => Main::Chars($_POST["prefix"])
                    ];

                    $_SESSION["data"][1] = true;
                    Main::Redirect("./install.php?setup=2");
                } else {
                    $error = "Database contains some tables, please remove them.";
                }   
            } else {
                $error = Database::$error;
            }
        } else {
            $error = Install::$lasterror;
        }
    } else if ($part == 2) {
        if (Install::validate_2($_POST)) {


            $_SESSION["data"]["bot"] = [
                "d_port" => Main::Chars($_POST["D_Port"]),
                "folder" => Main::Chars($_POST["Folder"]),
                "usedp" => Main::Chars($_POST["UseDP"]),
                "dpassword" => Main::Chars($_POST["DPassword"])
            ];

            $_SESSION["data"][2] = true;
            Main::Redirect("./install.php?setup=3");
        } else {
            $error = Install::$lasterror;
        }
    } else if ($part == 3) {
        if (Install::validate_3($_POST)) {
            $_SESSION["data"]["ssh"] = [
                "address" => Main::Chars($_POST["address"]),
                "username" => Main::Chars($_POST["username"]),
                "password" => Main::Chars($_POST["password"])
            ];
            $_SESSION["data"][3] = true;
            Main::Redirect("./install.php?setup=4");
        } else {
            $error = Install::$lasterror;
        }
    } else if ($part == 4) {
        if (Install::validate_5($_POST)) {
            $_SESSION["data"]["user"] = [
                "username" => Main::Chars($_POST["username"]),
                "password" => Main::Chars($_POST["password"])
            ];

            # Prepare config
            $config = file_get_contents("http://proxy.patrick115.eu/bot/Config.txt");

            $config = str_replace([
                #Database Block
                "\"address\" => \"127.0.0.1\",",
                "\"port\" => 3306,",
                "\"username\" => \"user\",",
                "\"password\" => \"example\"",
                "\"database\" => \"database\",",
                "\"prefix\" => \"sinusbot_\",",

                #Bot Block
                "\"d_port\" => 9987,",
                "\"folder\" => \"/opt\",",
                "\"usedp\" => true,",
                "\"dpassword\" => \"example123456\",",

                #SSH Block
                "\"address\" => \"10.10.10.10\",",
                "\"username\" => \"User\",",
                "\"password\" => \"example123456\","
            ], [
                #Database Block
                "\"address\" => \"" . $_SESSION["data"]["database"]["address"] . "\",",
                "\"port\" => " . $_SESSION["data"]["database"]["port"] . ",",
                "\"username\" => \"" . $_SESSION["data"]["database"]["username"] . "\",",
                "\"password\" => \"" . $_SESSION["data"]["database"]["password"] . "\"",
                "\"database\" => \"" . $_SESSION["data"]["database"]["database"] . "\",",
                "\"prefix\" => \"" . $_SESSION["data"]["database"]["prefix"] . "\",",

                #Bot Block
                "\"d_port\" => " . $_SESSION["data"]["bot"]["d_port"] . ",",
                "\"folder\" => \"" . $_SESSION["data"]["bot"]["folder"] . "\",",
                "\"usedp\" => " . $_SESSION["data"]["bot"]["usedp"] . ",",
                "\"dpassword\" => \"" . $_SESSION["data"]["bot"]["dpassword"] . "\",",

                #SSH Block
                "\"address\" => \"" . $_SESSION["data"]["ssh"]["address"] . "\",",
                "\"username\" => \"" . $_SESSION["data"]["ssh"]["username"] . "\",",
                "\"password\" => \"" . $_SESSION["data"]["ssh"]["password"] . "\","
            ],
                $config
            );
            #-----

            $file = fopen(__DIR__ . "/src/config/config.php", "w");
            fwrite($file, $config);
            fclose($file);

            $_SESSION["data"][4] = true;
            Main::Redirect("./install.php?setup=5");
        } else {
            $error = Install::$lasterror;
        }
    } else if ($part == 5) {

        $_SESSION["data"][5] = true;
        Main::Redirect("./install.php?setup=6");
  
    }
    
}


ob_flush();
flush();
if ($part == "6") {
    echo "<pre id=\"install\">Status:<br><div id=\"log\"></div></pre><script src=\"https://code.jquery.com/jquery-3.4.1.min.js\" integrity=\"sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=\" crossorigin=\"anonymous\"></script>";
    Install::Install_bot(__DIR__);
}


Error::returnError();

?>

<!DOCTYPE HTML>
<!--
	Identity by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>

<head>
    <title>Install | SinusBot Administration</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <noscript>
        <link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
</head>

<body class="is-preload">

    <!-- Wrapper -->
    <div id="wrapper">

        <!-- Main -->
        <section id="main">
            <header>
                <span class="avatar"><img src="images/avatar.png" alt="" width="120px" height="120px" /></span>
                <h1>Setup - <?=$install[$part]?></h1>
                <?php if (isset($error)): ?>
                <h3 style="color:red"><?=$error;?></h3>
                <?php endif;?>
            </header>
            <?php include_once __DIR__ . "/{$source[$part]}"?>
        </section>

        <!-- Footer -->
        <footer id="footer">
            <ul class="copyright">
                <li>&copy;<?php $release = (int) 2019; if((int) date("Y") > $release){ echo $release . "-" . date("Y");} else {echo date("Y");} ?> <a class="icon brands fa-github"></a><a target="_blank" href="https://github.com/patrick11514">patrick115</a></li>
                </li>
            </ul>
            <ul class="copyright">
                <li>&copy; Jane Doe</li>
                <li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
            </ul>
        </footer>

    </div>

    <!-- Scripts -->
    <script>
    if ('addEventListener' in window) {
        window.addEventListener('load', function() {
            document.body.className = document.body.className.replace(/\bis-preload\b/, '');
        });
        document.body.className += (navigator.userAgent.match(/(MSIE|rv:11\.0)/) ? ' is-ie' : '');
    }
    </script>
</body>

</html>