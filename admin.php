<?php
session_start();

// 1. Pemeriksaan sesi dipindahkan ke atas sebelum ada output HTML
if (!isset($_SESSION['sesi'])) {
    // 2. Logika redirect yang benar (tanpa echo sebelumnya)
    header("Location: login.php");
    exit(); // Penting: Hentikan eksekusi skrip setelah redirect
}

// 3. Logika untuk judul halaman dinamis (opsional, tapi bagus)
$page_title = "Dashboard"; // Judul default
if (isset($_GET['p'])) {
    switch ($_GET['p']) {
        case 'listmember':
            $page_title = "Data Anggota";
            break;
        case 'listbook':
            $page_title = "Data Buku";
            break;
        case 'addmember':
            $page_title = "Tambah Anggota";
            break;
        case 'listtransaksi':
            $page_title = "Transaksi";
            break;
        case 'transaksi':
            $page_title = "Daftar Transaksi";
            break;
        case 'report_book':
            $page_title = "Laporan Buku";
            break;
        case 'report_member':
            $page_title = "Laporan Anggota";
            break;
        default:
            $page_title = "Halaman Tidak Ditemukan";
    }
}

// Database connection
$host = 'localhost';
$dbname = 'perpustakaan_db';
$username = 'root'; // Default XAMPP username
$password = '';     // Default XAMPP password (empty)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Digital Library | <?php echo htmlspecialchars($page_title); ?></title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
        </ul>
    </nav>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="admin.php" class="brand-link">
            <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">Digital Library</span>
        </a>

        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="dist/img/<?php echo isset($_SESSION['foto_profil']) ? htmlspecialchars($_SESSION['foto_profil']) : 'user2-160x160.jpg'; ?>" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block"><?php echo htmlspecialchars($_SESSION['sesi']); ?></a>
                </div>
            </div>

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="admin.php" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-header">MANAJEMEN DATA</li>
                    <li class="nav-item">
                        <a href="admin.php?p=listmember" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Data Anggota</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="admin.php?p=listbook" class="nav-link">
                            <i class="nav-icon fas fa-book"></i>
                            <p>Data Buku</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="admin.php?p=transaksi" class="nav-link">
                            <i class="nav-icon fas fa-exchange-alt"></i>
                            <p>Daftar Transaksi</p>
                        </a>
                    </li>
                    <li class="nav-header">LAPORAN</li>
                    <li class="nav-item">
                        <a href="admin.php?p=report_book" class="nav-link">
                            <i class="nav-icon fas fa-print"></i>
                            <p>Laporan Buku</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="admin.php?p=report_member" class="nav-link">
                            <i class="nav-icon fas fa-print"></i>
                            <p>Laporan Anggota</p>
                        </a>
                    </li>
                    <li class="nav-header">AKUN</li>
                    <li class="nav-item">
                        <a href="logout.php" class="nav-link bg-danger">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <?php 
            $p_dir = 'views/admin';
            if (!empty($_GET['p'])) {
                $pages = scandir($p_dir, 0);
                unset($pages[0], $pages[1]);
                $view = $_GET['p'];
                if (in_array($view . '.php', $pages)) {
                    include($p_dir . '/' . $view . '.php');
                } else {
                    include('views/notfound.php'); 
                }
            } else {
                include($p_dir . '/dashboard.php'); 
            }
        ?>
    </div>
    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <b>Version</b> 1.0
        </div>
        <strong>Copyright © <?php echo date('Y'); ?> <a href="#">Digital Library</a>.</strong> All rights reserved.
    </footer>

    <aside class="control-sidebar control-sidebar-dark"></aside>
</div>
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
<script>
    $(function () {
        if ($('#example1').length) {
            $("#example1").DataTable({
                "responsive": true, "lengthChange": true, "autoWidth": false,
            });
        }
    });
</script>
</body>
</html>