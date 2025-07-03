<?php
// Sertakan koneksi database
include 'koneksi.php';

// --- BAGIAN PERSIAPAN DATA ---

// 1. Ambil nilai filter dari URL (jika ada)
$selected_type = isset($_GET['type']) ? $_GET['type'] : '';
$selected_status = isset($_GET['status']) ? $_GET['status'] : '';

// 2. Bangun query SQL secara dinamis dan aman berdasarkan filter
$sql = "SELECT member_id, firstname, lastname, email, gender, address, contact, type, status FROM member";

// Siapkan array untuk menampung kondisi WHERE dan parameter
$conditions = [];
$params = [];
$types = '';

// Tambahkan kondisi untuk 'type' jika dipilih
if (!empty($selected_type)) {
    $conditions[] = "type = ?";
    $params[] = $selected_type;
    $types .= 's'; // s untuk string
}

// Tambahkan kondisi untuk 'status' jika dipilih.
// Kita cek dengan `!== ''` karena status bisa jadi '0'
if ($selected_status !== '') {
    $conditions[] = "status = ?";
    $params[] = $selected_status;
    $types .= 'i'; // i untuk integer
}

// Gabungkan semua kondisi dengan 'AND' jika ada lebih dari satu
if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

$sql .= " ORDER BY firstname ASC";

// Persiapkan dan eksekusi statement untuk keamanan
$stmt = mysqli_prepare($db, $sql);

// Bind parameter jika ada
if (!empty($types)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Laporan Data Anggota</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="admin.php">Home</a></li>
                    <li class="breadcrumb-item active">Laporan Anggota</li>
                </ol>
            </div>
        </div>
    </div></section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Filter Laporan</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="admin.php">
                    <input type="hidden" name="p" value="report_member">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tipe Anggota</label>
                                <select name="type" class="form-control">
                                    <option value="">-- Semua Tipe --</option>
                                    <option value="Guru" <?php if ($selected_type == 'Guru') echo 'selected'; ?>>Guru</option>
                                    <option value="Siswa" <?php if ($selected_type == 'Siswa') echo 'selected'; ?>>Siswa</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">-- Semua Status --</option>
                                    <option value="1" <?php if ($selected_status === '1') echo 'selected'; ?>>Aktif</option>
                                    <option value="0" <?php if ($selected_status === '0') echo 'selected'; ?>>Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary form-control">Tampilkan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Hasil Laporan Anggota</h3>
            </div>
            <div class="card-body">
                <table id="reportMemberTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No.</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Jenis Kelamin</th>
                            <th>Kontak</th>
                            <th>Tipe</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && mysqli_num_rows($result) > 0) {
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                                <tr>
                                    <td class="text-center"><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo $row['gender'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                                    <td><?php echo htmlspecialchars($row['contact']); ?></td>
                                    <td><?php echo htmlspecialchars($row['type']); ?></td>
                                    <td class="text-center">
                                        <?php
                                            if ($row['status'] == 1) {
                                                echo '<span class="badge badge-success">Aktif</span>';
                                            } else {
                                                echo '<span class="badge badge-danger">Tidak Aktif</span>';
                                            }
                                        ?>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo '<tr><td colspan="7" class="text-center">Tidak ada data untuk ditampilkan sesuai filter.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            </div>
        </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    if ($('#reportMemberTable').length) {
        $("#reportMemberTable").DataTable({
            // Aktifkan tombol-tombol ekspor
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
            "responsive": true, 
            "lengthChange": true,
            "autoWidth": false,
        }).buttons().container().appendTo('#reportMemberTable_wrapper .col-md-6:eq(0)');
    }
  });
</script>