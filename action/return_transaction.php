<?php
// Selalu mulai sesi di awal (penting untuk otorisasi di masa depan)
session_start();

// Sertakan file koneksi ke database
include '../koneksi.php';

// --- Validasi Input ---
// 1. Periksa apakah pengguna memiliki izin (contoh otorisasi sederhana)
// if (!isset($_SESSION['sesi']) || $_SESSION['role'] !== 'admin') {
//     die("Akses ditolak. Anda tidak memiliki izin.");
// }

// 2. Pastikan ID transaksi diterima dari URL dan tidak kosong
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Akses tidak sah. ID Transaksi tidak ditemukan.");
}

$borrow_id = $_GET['id'];
$return_date = date('Y-m-d'); // Tanggal pengembalian adalah hari ini

// --- Mulai Proses Database ---
// Menggunakan Database Transaction untuk memastikan integritas data (prinsip "All or Nothing")
mysqli_begin_transaction($db);

try {
    // LANGKAH 1: Update status transaksi utama di tabel `borrow` menjadi 'Selesai'
    // Menggunakan logika status: 1 = Selesai
    $sql_update_borrow = "UPDATE borrow SET status = 1 WHERE borrow_id = ?";
    $stmt_borrow = mysqli_prepare($db, $sql_update_borrow);
    mysqli_stmt_bind_param($stmt_borrow, "i", $borrow_id);
    mysqli_stmt_execute($stmt_borrow);
    
    // LANGKAH 2: Update semua detail buku yang terkait dengan transaksi ini di tabel `borrowdetails`
    // Ubah borrow_status menjadi 1 (dikembalikan) dan isi date_return
    $sql_update_details = "UPDATE borrowdetails SET borrow_status = 1, date_return = ? WHERE borrow_id = ?";
    $stmt_details = mysqli_prepare($db, $sql_update_details);
    mysqli_stmt_bind_param($stmt_details, "si", $return_date, $borrow_id);
    mysqli_stmt_execute($stmt_details);

    // LANGKAH 3: Update status semua buku yang dikembalikan menjadi 'Tersedia' di tabel `book`
    // Menggunakan subquery untuk menemukan semua book_id yang relevan
    $sql_update_books = "UPDATE book SET status = 1 WHERE book_id IN (SELECT book_id FROM borrowdetails WHERE borrow_id = ?)";
    $stmt_books = mysqli_prepare($db, $sql_update_books);
    mysqli_stmt_bind_param($stmt_books, "i", $borrow_id);
    mysqli_stmt_execute($stmt_books);

    // Jika semua query di atas berhasil tanpa error, simpan perubahan secara permanen
    mysqli_commit($db);
    
    // Redirect kembali ke halaman daftar transaksi dengan pesan sukses
    header("Location: ../admin.php?p=listtransaksi&status=return_success");
    exit();

} catch (mysqli_sql_exception $exception) {
    // Jika ada satu saja query yang gagal, batalkan SEMUA perubahan yang sudah terjadi
    mysqli_rollback($db);
    
    // Redirect kembali dengan pesan error
    // Anda bisa juga mencatat error ini ke dalam file log untuk debugging
    // error_log($exception->getMessage());
    header("Location: ../admin.php?p=listtransaksi&status=return_failed");
    exit();
} finally {
    // Selalu tutup koneksi pada akhirnya
    mysqli_close($db);
}
?>