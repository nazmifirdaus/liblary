<?php
// Pastikan file koneksi sudah disertakan di awal
include '../koneksi.php';

// Periksa apakah koneksi database berhasil
if ($db === false) {
    die("ERROR: Tidak dapat terhubung ke database. " . mysqli_connect_error());
}

if (isset($_POST['submit'])) {

    // 1. Ambil dan Sanitasi Input untuk mencegah XSS
    $firstname = htmlspecialchars($_POST['firstname']);
    $lastname  = htmlspecialchars($_POST['lastname']);
    $gender    = htmlspecialchars($_POST['gender']);
    $alamat    = htmlspecialchars($_POST['address']);
    $phone     = htmlspecialchars($_POST['contact']);
    $tipe      = htmlspecialchars($_POST['type']);
    $email     = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); // Sanitasi email
    $idx       = isset($_POST['id']) ? $_POST['id'] : null;

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Jika email tidak valid, hentikan proses dan berikan pesan
        die("Format email tidak valid.");
    }

    // Cek apakah ini proses update atau insert
    if (!empty($idx)) {
        // --- PROSES UPDATE ---
        // 2. Gunakan Prepared Statements untuk UPDATE
        $dml = "UPDATE member SET firstname=?, lastname=?, email=?, gender=?, address=?, contact=?, type=? WHERE member_id=?";
        
        // Siapkan statement
        if ($stmt = mysqli_prepare($db, $dml)) {
            // Bind variabel ke statement sebagai parameter
            // Tipe data: s = string, i = integer
            mysqli_stmt_bind_param($stmt, "sssssssi", $firstname, $lastname, $email, $gender, $alamat, $phone, $tipe, $idx);
        } else {
            die("ERROR: Gagal mempersiapkan query UPDATE. " . mysqli_error($db));
        }

    } else {
        // --- PROSES INSERT ---
        // 2. Gunakan Prepared Statements untuk INSERT
        $dml = "INSERT INTO member(firstname, lastname, email, gender, address, contact, type) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        // Siapkan statement
        if ($stmt = mysqli_prepare($db, $dml)) {
            // Bind variabel ke statement sebagai parameter
            mysqli_stmt_bind_param($stmt, "sssssss", $firstname, $lastname, $email, $gender, $alamat, $phone, $tipe);
        } else {
            die("ERROR: Gagal mempersiapkan query INSERT. " . mysqli_error($db));
        }
    }

    // 3. Eksekusi statement dan lakukan penanganan error
    if (mysqli_stmt_execute($stmt)) {
        // Jika berhasil, arahkan ke halaman daftar member
        header("location: ../admin.php?p=listmember&status=sukses");
    } else {
        // Jika gagal, tampilkan pesan error
        echo "ERROR: Gagal mengeksekusi query. " . mysqli_stmt_error($stmt);
    }

    // Selalu tutup statement
    mysqli_stmt_close($stmt);

}

// Tutup koneksi database
mysqli_close($db);

?>