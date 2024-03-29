<?php

use patrick115\Sinusbot\Main;
use patrick115\Sinusbot\Session;
use patrick115\Sinusbot\Stats;
include "../src/Class.php";

if (!Session::get("logged")) {
    Main::Redirect("login");
}

$nav = [
    "info"       => "#",
    "bots"       => "./bots",
    "createuser" => "./addusr",
    "settings"   => [
        "database" => "./settings/database",
        "bot"      => "./settings/bot",
        "ssh"      => "./settings/ssh",
        "other"    => "./settings/other",
    ],
];

$active = [
    "info"       => "active",
    "bots"       => "",
    "createuser" => "",
    "settings_s" => "",
    "settings"   => [
        "database" => "",
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
    <title>Info | <?=$_SERVER['SERVER_NAME']?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="css/adminlte.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <!-- Favicon -->
    <link rel="shortcut icon" href="../images/avatar.png" type="image/x-icon">
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
                <img src="../images/avatar.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
                    style="opacity: .8">
                <span class="brand-text font-weight-light">SinusBot</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="../images/empty_user_icon_256.v2.png" class="img-circle elevation-2" alt="User Image">
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
                            <h1>Info</h1>
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
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <!-- /.info-box -->
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-user-plus"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Users</span>
                                <span class="info-box-number"><?=Stats::init()->getRegistredUsers()?></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <!-- /.info-box -->
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-robot"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Bots</span>
                                <span class="info-box-number"><?=Stats::init()->getBots()?></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
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
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="plugins/adminlte/adminlte.min.js"></script>
<!-- Sweat Alerts 2-->
<script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>
<!-- Data Tables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<!-- Year Script -->
<script>
$('#year').text(new Date().getFullYear());
</script>

</html>