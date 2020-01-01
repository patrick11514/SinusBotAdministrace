<?php

use patrick115\Sinusbot\Main;
use patrick115\Sinusbot\Session;
use patrick115\Sinusbot\Error;
use patrick115\Sinusbot\Install;

if ($installer !== true) {
    die();
}

include __DIR__ . "/src/Class.php";

if (empty($_GET["part"])) {
    Main::Redirect("./install.php?part=1");
    Session::destroy();
} else {
    if (Main::Chars($_GET["part"]) > 3 || Main::Chars($_GET["part"]) < 1) {
        Main::Redirect("./install.php?part=1");
        Session::destroy();
    }
}

if (empty($_SESSION["data"])) {
    $_SESSION["data"] = [
        1 => false,
        2 => false,
        3 => false
    ];
}

$part = Main::Chars($_GET["part"]);

if ($_SESSION["data"][$part - 1] === false && $part > 1) {
    Main::Redirect("./install.php?part=" . ($part - 1));
} 

$errorc = Error::init();

$main = new Main();

if (isset($_POST) && isset($_POST["submit"])) {
    if ($part == 1) {
        if (empty($_POST["username"]) || empty($_POST["password"])) {
            $error = "Please fill form!";
        } else {

            if ($main->validateCredentials(
                Main::Chars($_POST["username"]),
                Main::Chars($_POST["password"])
            )) {
                $_SESSION["data"][1] = true;
                Main::Redirect("./install.php?part=2");
            } else {
                $error = $errorc->getError();
                echo "aaa";
            }
        }
    } else if ($part == 2) {
        $_SESSION["data"][2] = true;
        Main::Redirect("./install.php?part=3");
    }
}

if ($part == 3) {
    echo "<pre><div id=\"install_log\"></pre><script src=\"https://code.jquery.com/jquery-3.4.1.min.js\" integrity=\"sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=\" crossorigin=\"anonymous\"></script>";
    Install::run_reinstall();
    $errors->returnError();
}



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
    <link rel="stylesheet" href="assets/css/main<?php if ($part == 3) echo "-fast" ?>.css" />
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
                <h1>Reinstall</h1>
                <?php if (isset($error)): ?>
                <h3 style="color:red"><?=$error;?></h3>
                <?php endif;?>
            </header>
            <?php include __DIR__ . "/src/installer/reinstall_part_" . Main::Chars($_GET["part"] . ".html"); ?>
        </section>

        <!-- Footer -->
        <footer id="footer">
            <ul class="copyright">
                <li>&copy;<?php $release = (int) 2019; if((int) date("Y") > $release){ echo $release . "-" . date("Y");} else {echo date("Y");} ?> <i class="icon brands fa-github"></i><a target="_blank" href="https://github.com/patrick11514">patrick115</a></li>
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