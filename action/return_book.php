<?php
// Sertakan koneksi database
include '../koneksi.php';

// Validasi input: pastikan ID detail dan ID borrow ada di URL
if (!isset($_GET['detail_id']) || !isset($_GET['borrow_id'])) {
    die("Akses tidak sah. ID tidak ditemukan.");
}

$detail_id = $_GET['detail_id'];
$borrow_id = $_GET['borrow_id'];
$return_date = date('Y-m-d'); // Tanggal pengembalian adalah hari ini

// Mulai Database Transaction untuk menjaga integritas data
mysqli_begin_transaction($db);

try {
    // Langkah 1: Update status buku di tabel 'borrowdetails'
    // Ubah borrow_status menjadi 1 (dikembalikan) dan isi date_return
    $sql_update_detail = "UPDATE borrowdetails SET borrow_status = 1, date_return = ? WHERE borrow_details_id = ?";
    $stmt_update_detail = mysqli_prepare($db, $sql_update_detail);
    mysqli_stmt_bind_param($stmt_update_detail, "si", $return_date, $detail_id);
    mysqli_stmt_execute($stmt_update_detail);

    // (Opsional tapi direkomendasikan) Update juga status ketersediaan buku di tabel 'book'
    // Pertama, cari tahu book_id dari borrow_details_id
    $sql_get_book_id = "SELECT book_id FROM borrowdetails WHERE borrow_details_id = ?";
    $stmt_get_book_id = mysqli_prepare($db, $sql_get_book_id);
    mysqli_stmt_bind_param($stmt_get_book_id, "i", $detail_id);
    mysqli_stmt_execute($stmt_get_book_id);
    $result_book_id = mysqli_stmt_get_result($stmt_get_book_id);
    if ($row_book_id = mysqli_fetch_assoc($result_book_id)) {
        $book_id_to_update = $row_book_id['book_id'];
        
        // Sekarang update status buku menjadi tersedia (misal: status 1 = tersedia)
        $sql_update_book_stock = "UPDATE book SET status = 1 WHERE book_id = ?";
        $stmt_update_book_stock = mysqli_prepare($db, $sql_update_book_stock);
        mysqli_stmt_bind_param($stmt_update_book_stock, "i", $book_id_to_update);
        mysqli_stmt_execute($stmt_update_book_stock);
    }
    
    // Langkah 2: Cek apakah semua buku dalam transaksi ini sudah dikembalikan
    $sql_check_all_returned = "SELECT COUNT(*) as pending_books FROM borrowdetails WHERE borrow_id = ? AND borrow_status = 0";
    $stmt_check = mysqli_prepare($db, $sql_check_all_returned);
    mysqli_stmt_bind_param($stmt_check, "i", $borrow_id);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    $pending_count = mysqli_fetch_assoc($result_check)['pending_books'];
    
    // Langkah 3: Jika sudah tidak ada buku yang berstatus 'dipinjam', update status transaksi utama
    if ($pending_count == 0) {
        $sql_update_borrow = "UPDATE borrow SET status = 1 WHERE borrow_id = ?"; // 1 = Selesai
        $stmt_update_borrow = mysqli_prepare($db, $sql_update_borrow);
        mysqli_stmt_bind_param($stmt_update_borrow, "i", $borrow_id);
        mysqli_stmt_execute($stmt_update_borrow);
    }
    
    // Jika semua query berhasil, simpan perubahan secara permanen
    mysqli_commit($db);
    
    // Redirect kembali ke halaman detail dengan pesan sukses
    header("Location: ../admin.php?p=detailtransaksi&id=" . $borrow_id . "&status=return_success");
    exit();

} catch (mysqli_sql_exception $exception) {
    // Jika ada satu saja query yang gagal, batalkan semua perubahan
    mysqli_rollback($db);
    
    // Redirect kembali dengan pesan error
    header("Location: ../admin.php?p=detailtransaksi&id=" . $borrow_id . "&status=return_failed");
    exit();
}
?>