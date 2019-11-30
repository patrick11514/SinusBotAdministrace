<?php

session_start();

use patrick115\Sinusbot\Database;
use patrick115\Sinusbot\Install;
use patrick115\Sinusbot\Main;

include __DIR__ . "/src/Class.php";

include __DIR__ . "/src/installer/installs.php";



Install::checkVersion();

if (!is_writable(__DIR__)) {
    $error = "<pre>Folder " . __DIR__ . " is not writable!" . PHP_EOL . "Please use these 2 options to repair it:" . PHP_EOL . "<ul><li>Give this folder to user <b>www-data</b> <i>(recommended)</i></li><li>Give this folder 0777 permissions</li></ul>";
    die($error);
}

if (file_exists(__DIR__ . "/src/config/config.php")) {
    unlink(__DIR__ . "/src/config/config.php");
}

if (empty($_GET["setup"])) {
    Main::Redirect("./install.php?setup=1");
}

if (empty($_SESSION["temp"])) {
    $_SESSION["temp"] = Install::randomString(40);
    $_SESION["data"] = [
        1 => false,
        2 => false,
        3 => false,
    ];
    fopen(__DIR__ . "/temp_" . $_SESSION["temp"] . ".txt", "w");
    Main::Redirect("./install.php?setup=1");
}

$part = Main::Chars($_GET["setup"]);

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
                $config = file_get_contents("http://proxy.patrick115.eu/bot/Config.txt");

                $config = str_replace(
                    "\"address\" => \"127.0.0.1\",",
                    "\"address\" => \"" . Main::Chars($_POST["address"]) . "\",",
                    str_replace(
                        "\"port\" => 3306,",
                        "\"port\" => " . Main::Chars($_POST["port"]) . ",",
                        str_replace(
                            "\"username\" => \"user\",",
                            "\"username\" => \"" . Main::Chars($_POST["username"]) . "\",",
                            str_replace(
                                "\"password\" => \"example\"",
                                "\"password\" => \"" . Main::Chars($_POST["password"]) . "\"",
                                str_replace(
                                    "\"database\" => \"database\",",
                                    "\"database\" => \"" . Main::Chars($_POST["database"]) . "\",",
                                    $config
                                )
                            )
                        )
                    )
                );
                $file = fopen(__DIR__ . "/temp_" . $_SESSION["temp"] . ".txt", "w");
                fwrite($file, $config);
                fclose($file);
                Main::Redirect("./install.php?setup=2");
            } else {
                echo "ERROR";
                $error = Database::$error;
            }
        } else {
            $error = Install::$lasterror;
        }
    } else if ($part == 2) {
        if (Install::validate_2($_POST)) {
            $config = file_get_contents(__DIR__ . "/temp_" . $_SESSION["temp"] . ".txt");
            unlink(__DIR__ . "/temp_" . $_SESSION["temp"] . ".txt");
            $file = fopen(__DIR__ . "/temp_" . $_SESSION["temp"] . ".txt", "w");
            $config = str_replace(
                "\"D_Port\" => 9987,",
                "\"D_Port\" => " . Main::Chars($_POST["D_Port"]) . ",",
                str_replace(
                    "\"Folder\" => \"/opt\",",
                    "\"Folder\" => \"" . Main::Chars($_POST["Folder"]) . "\",",
                    str_replace(
                        "\"UseDP\" => true,",
                        "\"UseDP\" => " . Main::Chars($_POST["UseDP"]) . ",",
                        str_replace(
                            "\"DPassword\" => \"example123456\",",
                            "\"DPassword\" => \"" . Main::Chars($_POST["DPassword"]) . "\",",
                            $config
                        )
                    )
                )
            );
            fwrite($file, $config);
            fclose($file);
            Main::Redirect("./install.php?setup=3");
        } else {
            $error = Install::$lasterror;
        }
    } else if ($part == 3) {
        if (Install::validate_3($_POST)) {
            $config = file_get_contents(__DIR__ . "/temp_" . $_SESSION["temp"] . ".txt");
            unlink(__DIR__ . "/temp_" . $_SESSION["temp"] . ".txt");
            $file = fopen(__DIR__ . "/temp_" . $_SESSION["temp"] . ".txt", "w");
            $config = str_replace(
                "\"address\" => \"10.10.10.10\",",
                "\"address\" => \"" . $_POST["address"] . "\",",
                str_replace(
                    "\"username\" => \"User\",",
                    "\"username\" => \"" . $_POST["username"] . "\",",
                    str_replace(
                        "\"password\" => \"example123456\",",
                        "\"password\" => \"" . $_POST["password"] . "\",",
                        $config
                    )
                )
            );
            fwrite($file, $config);
            fclose($file);
            Main::Redirect("./install.php?setup=4");
        } else {
            $error = Install::$lasterror;
        }
    } else if ($part == 4) {
        Install::select(["test", "bagr", "gdfgdgf"], "aa", "aa");
        #Install::Install_bot(__DIR__ . "/temp_" . $_SESSION["temp"] . ".txt");
    }
}

?>
<pre>
    <?php print_r($_POST);?>
</pre>
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
                <li>&copy;<?=date("Y")?> <a class="icon brands fa-github"></a><a target="_blank" href="https://github.com/patrick11514">patrick115</a></li>
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