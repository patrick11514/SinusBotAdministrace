<?php

use patrick115\Sinusbot\Config;
use patrick115\Sinusbot\Main;
use patrick115\Sinusbot\Session;

include "../../../src/Class.php";

if (!Session::get("logged")) {
    Main::Redirect("../../login?back=settings/database");
}

$db = [
    "host"     => Config::init()->getConfig("Database/address"),
    "username" => Config::init()->getConfig("Database/username"),
    "password" => Main::hide(Config::init()->getConfig("Database/password")),
    "database" => Config::init()->getConfig("Database/database"),
    "port"     => Config::init()->getConfig("Database/port"),
    "prefix"   => Config::init()->getConfig("Database/prefix"),
];

$nav = [
    "info"       => "../../",
    "bots"       => "../../bots",
    "createuser" => "../../addusr",
    "settings"   => [
        "database" => "#",
        "bot"      => "../bot",
        "ssh"      => "../ssh",
        "other"    => "../other",
    ],
];

$active = [
    "info"       => "",
    "bots"       => "",
    "createuser" => "",
    "settings_s" => "active",
    "settings"   => [
        "database" => "active",
        "bot"      => "",
        "ssh"      => "",
        "other"    => "",
    ],
];

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Settings | <?=$_SERVER['SERVER_NAME']?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="../../css/adminlte.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <!-- Favicon -->
    <link rel="shortcut icon" href="../../../images/avatar.png" type="image/x-icon">
</head>

<body class="hold-transition sidebar-mini">
    <!-- Site wrapper -->
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Sidebar -->
            <!-- Brand Logo -->
            <a href="#" class="brand-link">
                <img src="../../../images/avatar.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
                    style="opacity: .8">
                <span class="brand-text font-weight-light">SinusBot</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="../../../images/empty_user_icon_256.v2.png" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a class="d-block"><?=Session::get("username")?></a>
                    </div>
                </div>
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <h3><i class="fas fa-sign-out-alt img-circle elevation-2" style="color:black;padding:5px;"></i>
                        </h3>
                    </div>
                    <div class="info">
                        <a href="logout" class="d-block">Logout</a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <?php include MainDir . "/src/includes/sidebar.php"?>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Settings ➤ Database</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="card">
                    <div class="card-body">
                    <h2>Database</h2>
                        <?php if (!isset($_GET["edit"])): ?>
                        <h5>Host:     <code style="background-color: #dedede;border-radius: 4px;padding: 3px 6px 3px 6px;"><?=$db["host"]?></code></h5>
                        <h5>Port:     <code style="background-color: #dedede;border-radius: 4px;padding: 3px 6px 3px 6px;"><?=$db["port"]?></code></h5>
                        <h5>Username: <code style="background-color: #dedede;border-radius: 4px;padding: 3px 6px 3px 6px;"><?=$db["username"]?></code></h5>
                        <h5>Password: <code style="background-color: #dedede;border-radius: 4px;padding: 3px 6px 3px 6px;"><?=$db["password"]?></code></h5>
                        <h5>Database: <code style="background-color: #dedede;border-radius: 4px;padding: 3px 6px 3px 6px;"><?=$db["database"]?></code></h5>
                        <h5>Table Prefix: <code style="background-color: #dedede;border-radius: 4px;padding: 3px 6px 3px 6px;"><?=$db["prefix"]?></code></h5>
                        <a href="./?edit"><button type="button" class="btn btn-primary">Edit</button></a>
                        <?php else: ?>
                        <?php if (isset($_GET["error"])): ?>
                            <h4 style="color:red"><?=Main::Chars($_GET["error"])?></h4>
                        <?php endif;?>
                        <form action="./proceed.php" method="post">
                        <div class="form-group">
                            <label for="host">Host</label>
                            <input type="text" class="form-control" id="host" value="<?=$db["host"]?>" name="host" required>
                        </div>
                        <div class="form-group">
                            <label for="port">Port</label>
                            <input type="number" class="form-control" id="port" value="<?=$db["port"]?>" name="port" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" value="<?=$db["username"]?>" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="text" class="form-control" id="password" value="<?=$db["password"]?>" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="database">Database</label>
                            <input type="text" class="form-control" id="database" value="<?=$db["database"]?>" name="database" required>
                        </div>
                        <div class="form-group">
                            <label for="prefix">Table Prefix</label>
                            <input type="text" class="form-control" id="prefix" value="<?=$db["prefix"]?>" name="prefix" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="./"><button type="button" class="btn btn-primary">Back</button></a>
                        </form>
                        <?php endif;?>
                    </div>
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 3.0.0-rc.5
            </div>
            <strong>Copyright &copy; 2014-<span id="year"></span> <a href="http://adminlte.io">AdminLTE.io</a>.</strong>
            All rights reserved.
        </footer>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->
</body>

<?php
$errors->returnError();
?>

<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../../plugins/adminlte/adminlte.min.js"></script>
<!-- Sweat Alerts 2-->
<script src="../../plugins/sweetalert2/sweetalert2.all.min.js"></script>
<!-- Data Tables-->
<script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<!-- Year Script -->
<script>
$('#year').text(new Date().getFullYear());
</script>

</html>