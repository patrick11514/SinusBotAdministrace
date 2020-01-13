<?php
if (empty($_GET["key"])) {
    die("Invalid parameters!");
}

use patrick115\Sinusbot\Database;
use patrick115\Sinusbot\Main;

include "../src/Class.php";

$key = Main::Chars($_GET["key"]);

$db = Database::init();
$main = new Main();

$rv = $db->select(["creator", "for", "message", "key"], "invites", "LIMIT 1", "key", $key);

if ($db->num_rows($rv) == 0) {
    die("Invitation code not found!");
}

while ($row = $rv->fetch_assoc())
{
    $from = $main->getUserByID($row["creator"]);
    $for = $row["for"];
    $message = $row["message"];
}

$ret = $db->select(["id"], "users", "LIMIT 1", "username", $for);

if ($db->num_rows($rv) == 0) {
    $db->delete("invites", ["key"], [$key]);
    die("This invitation for $for has been already claimed.");
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
    <link rel="stylesheet" href="../assets/css/main.css" />
    <noscript>
        <link rel="stylesheet" href="../assets/css/noscript.css" /></noscript>
    <style>
        .ip-info {
            border: solid 1px #C6CACD;
            outline: 0;
            padding: 1px 10px;
        }
        .ta-info {
            width: auto;
            height: auto;
            border: solid 1px #C6CACD;
            outline: 0;
            padding: 1px 10px;
            overflow: hidden;
        }
    </style>
</head>

<body class="is-preload" onload="init();">

    <!-- Wrapper -->
    <div id="wrapper">

        <!-- Main -->
        <section id="main">
            <header>
                <span class="avatar"><img src="../images/avatar.png" alt="" width="120px" height="120px" /></span>
                <h1>Invitation</h1>
                <?php if (isset($_GET["e"])) echo "<h2 style=\"color:red\">" . Main::Chars($_GET["e"]) . "</h2>"; ?>
            </header>
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align:right"><h4>From:</h4></td>
                        <td><h4><input class="ip-info" value="<?=$from?>" disabled></h4></td>
                    </tr>
                    <tr>
                        <td style="text-align:right"><h4>For:</h4></td>
                        <td><h4><input class="ip-info" value="<?=$for?>" disabled></h4></td> 
                    </tr>
                    <tr>
                        <td style="text-align:right"><h4>Message:</h4></td> 
                        <td><h4><textarea class="ta-info" id="ta-info" disabled><?=$message?></textarea></h4></td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <form action="./confirm.php" method="post">
                <input type="hidden" name="key" id="key" value="<?=$key?>" readonly="" required="">
                <div class="fields">
                    <div class="field">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" value="<?=$for?>" required="" readonly="">
                    </div>
                    <div class="field">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" required="">
                    </div>
                    <div class="field">
                        <label for="2password">Again Password</label>
                        <input type="password" name="2password" id="2password" required="">
                    </div>
                </div>
                <ul class="actions special">
                    <li><input type="submit" name="submit" value="Register" id="" required></li>
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
    <script>
        if (window.attachEvent) {
            observe = function (element, event, handler) {
                element.attachEvent('on'+event, handler);
            };
        }
        else {
            observe = function (element, event, handler) {
                element.addEventListener(event, handler, false);
            };
        }
        function init () {
            var text = document.getElementById('ta-info');
            function resize () {
                text.style.height = 'auto';
                text.style.height = text.scrollHeight+'px';
            }
            /* 0-timeout to get the already changed text */
            function delayedResize () {
                window.setTimeout(resize, 0);
            }
            observe(text, 'change',  resize);
            observe(text, 'cut',     delayedResize);
            observe(text, 'paste',   delayedResize);
            observe(text, 'drop',    delayedResize);
            observe(text, 'keydown', delayedResize);
        
            text.focus();
            text.select();
            resize();
        }
    </script>
</body>

</html>