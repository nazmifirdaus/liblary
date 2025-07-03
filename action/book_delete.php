<?php
// Sertakan file koneksi ke database
// Pastikan path ini sudah benar sesuai struktur folder Anda
include '../koneksi.php';

// 1. Periksa apakah ID diterima dari URL dan tidak kosong
if (isset($_GET['id']) && !empty($_GET['id'])) {
    
    // Ambil ID buku
    $idx = $_GET['id'];

    // Periksa koneksi database
    if ($db === false) {
        die("ERROR: Tidak dapat terhubung. " . mysqli_connect_error());
    }

    // 2. Keamanan: Gunakan Prepared Statement untuk mencegah SQL Injection
    $dml = "DELETE FROM book WHERE book_id = ?";

    // Siapkan statement
    if ($stmt = mysqli_prepare($db, $dml)) {
        
        // Bind ID ke statement sebagai parameter integer ('i')
        mysqli_stmt_bind_param($stmt, "i", $idx);

        // 3. Eksekusi statement
        if (mysqli_stmt_execute($stmt)) {
            
            // 4. Periksa apakah ada baris yang terhapus
            // mysqli_stmt_affected_rows() akan mengembalikan 1 jika berhasil, 0 jika ID tidak ditemukan
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                // Jika berhasil, arahkan kembali ke halaman daftar buku dengan status sukses
                header("location: ../admin.php?p=listbook&status=delete_success");
                exit();
            } else {
                // Jika tidak ada baris yang terhapus (misal ID tidak ada)
                header("location: ../admin.php?p=listbook&status=delete_failed_not_found");
                exit();
            }

        } else {
            // Jika eksekusi gagal karena error lain
            die("ERROR: Gagal mengeksekusi query. " . mysqli_stmt_error($stmt));
        }

        // Tutup statement
        mysqli_stmt_close($stmt);

    } else {
        die("ERROR: Gagal mempersiapkan query. " . mysqli_error($db));
    }

    // Tutup koneksi
    mysqli_close($db);

} else {
    // Jika tidak ada ID yang dikirim melalui URL, berikan pesan error atau arahkan kembali
    die("Error: ID buku tidak ditemukan untuk dihapus.");
}
?>