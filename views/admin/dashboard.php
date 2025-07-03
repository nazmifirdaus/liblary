<?php
// ==========================================================
// BAGIAN PENGAMBILAN DATA DINAMIS
// ==========================================================

// 1. Sertakan file koneksi. Pastikan path ini benar!
// Dari 'views/admin/', kita perlu naik dua level untuk mencapai root.
include 'koneksi.php';

// Inisialisasi variabel dengan nilai default 0
$total_buku = 0;
$total_anggota = 0;
$total_transaksi_aktif = 0;

// 2. Query untuk menghitung total judul buku
$query_buku = mysqli_query($db, "SELECT COUNT(book_id) AS total FROM book");
if ($query_buku) {
    $total_buku = mysqli_fetch_assoc($query_buku)['total'];
}

// 3. Query untuk menghitung total anggota
$query_anggota = mysqli_query($db, "SELECT COUNT(member_id) AS total FROM member");
if ($query_anggota) {
    $total_anggota = mysqli_fetch_assoc($query_anggota)['total'];
}

// 4. Query untuk menghitung total transaksi yang masih aktif (status = 0 adalah 'Dipinjam')
$query_transaksi = mysqli_query($db, "SELECT COUNT(borrow_id) AS total FROM borrow WHERE status = 0");
if ($query_transaksi) {
    $total_transaksi_aktif = mysqli_fetch_assoc($query_transaksi)['total'];
}

// Ambil nama pengguna dari sesi untuk sapaan personal
$nama_user = isset($_SESSION['sesi']) ? htmlspecialchars($_SESSION['sesi']) : 'Pengguna';
?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div></section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> Selamat Datang!</h5>
                    Halo **<?php echo $nama_user; ?>**, selamat datang kembali di sistem Digital Library.
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?php echo $total_buku; ?></h3>
                        <p>Total Judul Buku</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <a href="admin.php?p=listbook" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?php echo $total_anggota; ?></h3>
                        <p>Total Anggota</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="admin.php?p=listmember" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?php echo $total_transaksi_aktif; ?></h3>
                        <p>Transaksi Peminjaman Aktif</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <a href="admin.php?p=listtransaksi" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            </div>
        </div>
</section>