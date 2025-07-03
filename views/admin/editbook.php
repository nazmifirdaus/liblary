<?php
// Sertakan file koneksi database. Pastikan path ini benar.
include 'koneksi.php';

// Inisialisasi variabel $row untuk menghindari error jika ID tidak ditemukan
$row = null;
$idx = isset($_GET['id']) ? $_GET['id'] : null;

if ($idx) {
    // Gunakan Prepared Statement untuk mengambil data dengan aman
    $dml = "SELECT * FROM book WHERE book_id = ?";
    
    if ($stmt = mysqli_prepare($db, $dml)) {
        mysqli_stmt_bind_param($stmt, "i", $idx);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    } else {
        die("Error preparing query: " . mysqli_error($db));
    }
}

if (!$row) {
    echo "<section class='content'><div class='container-fluid'><div class='alert alert-danger'>Buku dengan ID tersebut tidak ditemukan.</div></div></section>";
    exit;
}
?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Data Buku</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="admin.php?p=listbook">Data Buku</a></li>
                    <li class="breadcrumb-item active">Edit Buku</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Form Edit Buku: <?php echo htmlspecialchars($row['book_title']); ?></h3>
                    </div>
                    <form method="post" action="action/book_save.php">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['book_id']); ?>">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="book_title">Judul Buku</label>
                                        <input type="text" class="form-control" id="book_title" name="book_title" value="<?php echo htmlspecialchars($row['book_title']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="author">Penulis</label>
                                        <input type="text" class="form-control" id="author" name="author" value="<?php echo htmlspecialchars($row['author']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="category">Kategori</label>
                                        <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($row['category']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="isbn">ISBN</label>
                                        <input type="text" class="form-control" id="isbn" name="isbn" value="<?php echo htmlspecialchars($row['isbn']); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="publisher_name">Penerbit</label>
                                        <input type="text" class="form-control" id="publisher_name" name="publisher_name" value="<?php echo htmlspecialchars($row['publisher_name']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="copyright_year">Tahun Terbit</label>
                                        <input type="number" class="form-control" id="copyright_year" name="copyright_year" value="<?php echo htmlspecialchars($row['copyright_year']); ?>" min="1000">
                                    </div>
                                    <div class="form-group">
                                        <label for="book_copies">Jumlah Salinan</label>
                                        <input type="number" class="form-control" id="book_copies" name="book_copies" value="<?php echo htmlspecialchars($row['book_copies']); ?>" required min="0">
                                    </div>
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" name="status">
                                            <option value="1" <?php if ($row['status'] == '1') echo 'selected'; ?>>Tersedia</option>
                                            <option value="0" <?php if ($row['status'] == '0') echo 'selected'; ?>>Tidak Tersedia</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" name="submit" class="btn btn-primary">Update Data</button>
                            <a href="admin.php?p=listbook" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>