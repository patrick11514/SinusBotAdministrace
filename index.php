<?php

use patrick115\Sinusbot\Main;

include __DIR__ . "/src/Class.php";

if (!file_exists(__DIR__ . "/src/install.lock")) {
    Main::Redirect("./install.php");
}

$main = new Main();



?>

<!DOCTYPE HTML>
<!--
	Identity by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>

<head>
    <title>Login | SinusBot Administration</title>
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
                <h1>login</h1>

            </header>
            <form action="" method="post">
                <div class="fields">
                    <div class="field">
                        <input type="text" name="username" id="" placeholder="Username" required>
                    </div>
                    <div class="field">
                        <input type="password" name="password" id="" placeholder="Password" required>
                    </div>
                </div>
                <ul class="actions special">
                    <li><input type="submit" name="submit" placeholder="Login" id="" required></input></li>
                </ul>

            </form>
        </section>

        <!-- Footer -->
        <footer id="footer">
            <ul class="copyright">
                <li>&copy;<?= date("Y") ?> <a class="icon brands fa-github"></a><a target="_blank" href="https://github.com/patrick11514">patrick115</a>
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