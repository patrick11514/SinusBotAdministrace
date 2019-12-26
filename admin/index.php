<?php

use patrick115\Sinusbot\Main;
use patrick115\Sinusbot\Session;

include "../src/Class.php";

if (!Session::get("logged")) {
	Main::Redirect("./login");
}

$errors->returnError();

?>

<!DOCTYPE HTML>
<!--
	Miniport by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
	<head>
		<title>Administration | <?= $_SERVER['SERVER_NAME'] ?></title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
	</head>
	<body class="is-preload">

		<!-- Nav -->
			<nav id="nav">
				<ul class="container">
					<?//= //Main:: ?>
					<li><a href="#top">Info</a></li>
					<li><a href="#work">Work</a></li>
					<li><a href="#portfolio">Portfolio</a></li>
					<li><a href="#contact">Contact</a></li>
				</ul>
			</nav>

		<!-- Home -->
			<article id="top" class="wrapper style1">
				<div class="container">
					<div class="row">
						<div class="col-4 col-5-large col-12-medium">
							<span class="image fit"><img src="images/pic00.jpg" alt="" /></span>
						</div>
						<div class="col-8 col-7-large col-12-medium">
							<header>
								<h1>Hi. I'm <strong>Jane Doe</strong>.</h1>
							</header>
							<p>And this is <strong>Miniport</strong>, a free, fully responsive HTML5 site template designed by <a href="http://twitter.com/ajlkn">AJ</a> for <a href="http://html5up.net">HTML5 UP</a> &amp; released under the <a href="http://html5up.net/license">CCA license</a>.</p>
							<a href="#work" class="button large scrolly">Learn about what I do</a>
						</div>
					</div>
				</div>
			</article>

		<!-- Contact -->
			<article id="contact" class="wrapper style4">
				<div class="container medium">
						<div class="col-12">
							<ul class="social">
								<li><a target="_blank" href="https://fb.me/patrick115yt" class="icon brands fa-facebook-f"><span class="label">Facebook</span></a></li>
								<li><a target="_blank" href="https://github.com/patrick11514" class="icon brands fa-github"><span class="label">Github</span></a></li>
								<li><a target="_blank" href="https://patrick115.eu" class="icon solid fa-link"><span class="label">WebPage</span></a></li>
							</ul>
						</div>
					</div>
					<footer>
						<ul id="copyright">
            			    <li>&copy;<?php $release = (int) 2019; if((int) date("Y") > $release){ echo $release . "-" . date("Y");} else {echo date("Y");} ?> <a class="icon brands fa-github"></a><a target="_blank" href="https://github.com/patrick11514">patrick115</a></li>
            			    </li>
            			</ul>
						<br />
            			<ul id="copyright">
            			    <li>&copy; Jane Doe</li>
            			    <li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
            			</ul>
					</footer>
				</div>
			</article>

		<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/jquery.scrolly.min.js"></script>
			<script src="assets/js/browser.min.js"></script>
			<script src="assets/js/breakpoints.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>

	</body>
</html>