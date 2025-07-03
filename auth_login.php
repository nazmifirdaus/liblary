<?php
// Selalu mulai sesi di awal
session_start();

// Sertakan koneksi database
include "koneksi.php";

// Pastikan form disubmit
if (isset($_POST['submit'])) {

    $user = $_POST['user'];
    $pass = $_POST['password'];

    // Validasi dasar agar tidak kosong
    if (empty($user) || empty($pass)) {
        header("Location: login.php?error=empty");
        exit();
    }

    // 1. KEAMANAN: Gunakan Prepared Statements untuk mencegah SQL Injection
    $sql = "SELECT * FROM users WHERE username = ?";
    
    if ($stmt = mysqli_prepare($db, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $user);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Cek apakah user ditemukan
        if (mysqli_num_rows($result) == 1) {
            $data_user = mysqli_fetch_assoc($result);

            
            // 2. KEAMANAN: Verifikasi password yang di-hash
            if (password_verify($pass, $data_user['password'])) {
                // --- Login Berhasil ---

                // 3. KEAMANAN: Regenerasi ID Sesi untuk mencegah Session Fixation
                session_regenerate_id(true);

                // Simpan informasi pengguna ke dalam sesi
                $_SESSION['user_id'] = $data_user['user_id'];
                $_SESSION['sesi'] = $data_user['username']; // Atau 'nama_lengkap'
                // Anda juga bisa menyimpan info lain, seperti role
                // $_SESSION['role'] = $data_user['role'];

                // 4. Redirect ke halaman admin dengan cara yang benar
                header("Location: admin.php"); // atau admin.php
                exit();

            } else {
                // Password salah
                header("Location: login.php?error=wrongpassword");
                exit();
            }
        } else {
            // Username tidak ditemukan
            header("Location: login.php?error=nouser");
            exit();
        }

        mysqli_stmt_close($stmt);
    }
    mysqli_close($db);

} else {
    // Jika file diakses langsung tanpa submit, kembalikan ke halaman login
    header("Location: login.php");
    exit();
}
?>