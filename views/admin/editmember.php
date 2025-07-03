<?php
// Pastikan path ke file koneksi sudah benar.
include 'koneksi.php';

// Inisialisasi variabel $row untuk menghindari error jika ID tidak ditemukan
$row = null;
$idx = isset($_GET['id']) ? $_GET['id'] : null;

if ($idx) {
    // Kode pengambilan data Anda sudah sangat baik dan aman
    $dml = "SELECT * FROM member WHERE member_id = ?";
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

// Penanganan jika ID tidak ditemukan juga sudah tepat
if (!$row) {
    echo "<section class='content'><div class='container-fluid'><div class='alert alert-danger'>Anggota dengan ID tersebut tidak ditemukan.</div></div></section>";
    exit;
}
?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Anggota</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="admin.php?p=listmember">Data Anggota</a></li>
                    <li class="breadcrumb-item active">Edit Anggota</li>
                </ol>
            </div>
        </div>
    </div></section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Edit Anggota: <?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></h3>
                    </div>
                    <form method="post" action="action/member_save.php">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['member_id']); ?>">

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname">Nama Depan</label>
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($row['firstname']); ?>" id="firstname" name="firstname" placeholder="Nama Depan" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($row['email']); ?>" id="email" name="email" placeholder="Enter email" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="gender">Jenis Kelamin</label>
                                        <select class="form-control" id="gender" name="gender">
                                            <option value="L" <?php if ($row['gender'] == 'L') echo 'selected'; ?>>Laki-laki</option>
                                            <option value="P" <?php if ($row['gender'] == 'P') echo 'selected'; ?>>Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lastname">Nama Belakang</label>
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($row['lastname']); ?>" id="lastname" name="lastname" placeholder="Nama Belakang" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="contact">No Telp</label>
                                        <input type="tel" class="form-control" value="<?php echo htmlspecialchars($row['contact']); ?>" id="contact" name="contact" placeholder="Kontak">
                                    </div>
                                    <div class="form-group">
                                        <label for="type">Tipe Anggota</label>
                                        <select class="form-control" id="type" name="type">
                                            <option value="Guru" <?php if ($row['type'] == 'Guru') echo 'selected'; ?>>Guru</option>
                                            <option value="Siswa" <?php if ($row['type'] == 'Siswa') echo 'selected'; ?>>Siswa</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address">Alamat</label>
                                        <textarea class="form-control" id="address" name="address" placeholder="Alamat"><?php echo htmlspecialchars($row['address']); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" name="submit" class="btn btn-primary">Update Data</button>
                            <a href="admin.php?p=listmember" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>