<?php
use patrick115\Sinusbot\Main;
use patrick115\Sinusbot\Session;

include __DIR__ . "/../../src/Class.php";

if (Session::get("logged")) {
    Main::Redirect("../");
}

$login = new Main();

if (!empty($_POST) && $_POST["submit"]) {
    $error = $login->Login($_POST["username"], $_POST["password"]);
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
    <title>Login | <?= $_SERVER['SERVER_NAME'] ?></title>
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
                <h1>Login</h1>
                <?php if (isset($error)): ?>
                <h3 style="color:red"><?=$error;?></h3>
                <?php endif;?>
            </header>
            <form action="" method="post">
                <div class="fields">
                    <div class="field">
                        <input type="text" name="username" id="" required>
                    </div>
                    <div class="field">
                        <input type="password" name="password" id="" required>
                    </div>
                </div>
                <ul class="actions special">
                    <li><input type="submit" name="submit" value="Login" id="" required></li>
                </ul>
            </form>
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
<?php
$errors->returnError();
?>
</html>