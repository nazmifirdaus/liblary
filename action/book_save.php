<?php
// Sertakan file koneksi database
include '../koneksi.php';

// Periksa apakah form telah disubmit
if (isset($_POST['submit'])) {

    // Ambil semua data dari form (tanpa book_pub)
    $book_title     = $_POST['book_title'];
    $category       = $_POST['category'];
    $author         = $_POST['author'];
    $book_copies    = $_POST['book_copies'];
    $publisher_name = $_POST['publisher_name'];
    $isbn           = $_POST['isbn'];
    $copyright_year = $_POST['copyright_year'];
    $status         = $_POST['status'];
    
    // Cek apakah ada ID yang dikirim (menandakan ini adalah proses UPDATE)
    $idx = isset($_POST['id']) && !empty($_POST['id']) ? $_POST['id'] : null;

    if ($db === false) {
        die("ERROR: Tidak dapat terhubung. " . mysqli_connect_error());
    }

    // LOGIKA UNTUK MEMBEDAKAN INSERT ATAU UPDATE
    if ($idx) {
        // --- PROSES UPDATE ---
        // Query UPDATE tanpa kolom book_pub
        $dml = "UPDATE book SET book_title=?, category=?, author=?, book_copies=?, publisher_name=?, isbn=?, copyright_year=?, status=? 
                WHERE book_id=?";
        
        if ($stmt = mysqli_prepare($db, $dml)) {
            // Bind parameter, tipe string dan variabel disesuaikan
            mysqli_stmt_bind_param($stmt, "sssisissi",
                $book_title, $category, $author, $book_copies, $publisher_name, $isbn, $copyright_year, $status, $idx
            );
        } else {
            die("ERROR: Gagal mempersiapkan query UPDATE. " . mysqli_error($db));
        }

    } else {
        // --- PROSES INSERT ---
        // Query INSERT tanpa kolom book_pub
        $dml = "INSERT INTO book (book_title, category, author, book_copies, publisher_name, isbn, copyright_year, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($db, $dml)) {
            // Bind parameter untuk INSERT, tipe string dan variabel disesuaikan
            mysqli_stmt_bind_param($stmt, "sssisiss",
                $book_title, $category, $author, $book_copies, $publisher_name, $isbn, $copyright_year, $status
            );
        } else {
            die("ERROR: Gagal mempersiapkan query INSERT. " . mysqli_error($db));
        }
    }

    // Eksekusi statement yang sudah disiapkan
    if (mysqli_stmt_execute($stmt)) {
        header("location: ../admin.php?p=listbook&status=success");
        exit();
    } else {
        echo "ERROR: Gagal mengeksekusi query. " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($db);
}
?>