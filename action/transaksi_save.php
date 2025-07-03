<?php
// Sertakan koneksi
include '../koneksi.php';

// Periksa apakah form disubmit
if (isset($_POST['submit'])) {
    
    // Ambil data dari form
    $member_id = $_POST['member_id'];
    $book_ids = $_POST['book_ids']; 
    $date_borrow = $_POST['date_borrow'];
    $due_date = $_POST['due_date'];
    
    // ==========================================================
    // PERBAIKAN 1: Status transaksi utama harus 0 (Dipinjam) saat dibuat
    // ==========================================================
    $status = 0; 
    
    // Validasi dasar
    if (empty($member_id) || empty($book_ids)) {
        header("Location: ../admin.php?p=addtransaksi&status=error_input");
        exit();
    }
    
    // Mulai Database Transaction
    mysqli_begin_transaction($db);
    
    try {
        // Langkah 1: Simpan data utama ke tabel 'borrow' dengan status yang sudah diperbaiki
        $sql_borrow = "INSERT INTO borrow (member_id, date_borrow, due_date, status) VALUES (?, ?, ?, ?)";
        $stmt_borrow = mysqli_prepare($db, $sql_borrow);
        mysqli_stmt_bind_param($stmt_borrow, "issi", $member_id, $date_borrow, $due_date, $status);
        mysqli_stmt_execute($stmt_borrow);
        
        $new_borrow_id = mysqli_insert_id($db);
        
        // Langkah 2: Simpan setiap buku yang dipinjam ke tabel 'borrowdetails'
        $sql_details = "INSERT INTO borrowdetails (borrow_id, book_id, borrow_status) VALUES (?, ?, ?)";
        $stmt_details = mysqli_prepare($db, $sql_details);
        
        foreach ($book_ids as $book_id) {
            // ==============================================================
            // PERBAIKAN 2: Status detail buku harus 0 (Dipinjam) saat dibuat
            // ==============================================================
            $borrow_status = 0; 
            
            mysqli_stmt_bind_param($stmt_details, "iii", $new_borrow_id, $book_id, $borrow_status);
            mysqli_stmt_execute($stmt_details);

            // (Opsional) Langkah 3: Ubah status buku di tabel 'book' menjadi 'tidak tersedia' (misal: status 0)
            $sql_update_book = "UPDATE book SET status = 0 WHERE book_id = ?";
            $stmt_update_book = mysqli_prepare($db, $sql_update_book);
            mysqli_stmt_bind_param($stmt_update_book, "i", $book_id);
            mysqli_stmt_execute($stmt_update_book);
        }
        
        // Jika semua query berhasil, konfirmasi perubahan ke database
        mysqli_commit($db);
        
        header("Location: ../admin.php?p=listtransaksi&status=add_success");
        exit();
        
    } catch (mysqli_sql_exception $exception) {
        // Jika terjadi error, batalkan semua perubahan
        mysqli_rollback($db);
        
        // Redirect dengan pesan error
        header("Location: ../admin.php?p=addtransaksi&status=error_db");
        exit();
    }
    
} else {
    // Jika file diakses langsung, redirect ke halaman utama
    header("Location: ../admin.php");
    exit();
}
?>