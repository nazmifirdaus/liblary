<?php
// Sertakan koneksi database
include 'koneksi.php';

// --- BAGIAN PERSIAPAN DATA ---

// 1. Ambil semua tahun terbit yang unik dari tabel buku untuk mengisi dropdown filter
$years_query = mysqli_query($db, "SELECT DISTINCT copyright_year FROM book WHERE copyright_year IS NOT NULL ORDER BY copyright_year DESC");

// 2. Tentukan tahun yang dipilih dari filter. Jika tidak ada, kosongkan.
$selected_year = isset($_GET['year']) ? $_GET['year'] : '';

// 3. Bangun query utama untuk mengambil data buku berdasarkan filter
// Mulai dengan query dasar
$sql = "SELECT book_id, book_title, author, publisher_name, isbn, copyright_year, book_copies FROM book";

// Jika ada tahun yang dipilih, tambahkan kondisi WHERE
if (!empty($selected_year)) {
    // Menambahkan WHERE clause ke query dasar
    $sql .= " WHERE copyright_year = ?";
}

$sql .= " ORDER BY book_title ASC";

// Persiapkan statement untuk keamanan
$stmt = mysqli_prepare($db, $sql);

// Jika ada tahun yang dipilih, bind parameternya
if (!empty($selected_year)) {
    mysqli_stmt_bind_param($stmt, "s", $selected_year);
}

// Eksekusi query
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Laporan Data Buku</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="admin.php">Home</a></li>
                    <li class="breadcrumb-item active">Laporan Buku</li>
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
                    <input type="hidden" name="p" value="report_book">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tahun Terbit (Copyright Year)</label>
                                <select name="year" class="form-control">
                                    <option value="">-- Tampilkan Semua Tahun --</option>
                                    <?php
                                    if ($years_query) {
                                        while ($year_row = mysqli_fetch_assoc($years_query)) {
                                            $year = htmlspecialchars($year_row['copyright_year']);
                                            // Buat dropdown 'sticky' dengan memilih tahun yang sedang difilter
                                            $is_selected = ($year == $selected_year) ? 'selected' : '';
                                            echo "<option value='{$year}' {$is_selected}>{$year}</option>";
                                        }
                                    }
                                    ?>
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
                <h3 class="card-title">
                    Hasil Laporan Buku 
                    <?php 
                        // Judul dinamis berdasarkan filter
                        if (!empty($selected_year)) {
                            echo "Tahun " . htmlspecialchars($selected_year);
                        } else {
                            echo "- Semua Tahun";
                        }
                    ?>
                </h3>
            </div>
            <div class="card-body">
                <table id="reportTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No.</th>
                            <th>Judul Buku</th>
                            <th>Penulis</th>
                            <th>Penerbit</th>
                            <th>Tahun Terbit</th>
                            <th class="text-center">Jumlah</th>
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
                                    <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                                    <td><?php echo htmlspecialchars($row['author']); ?></td>
                                    <td><?php echo htmlspecialchars($row['publisher_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['copyright_year']); ?></td>
                                    <td class="text-center"><?php echo htmlspecialchars($row['book_copies']); ?></td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo '<tr><td colspan="6" class="text-center">Tidak ada data untuk ditampilkan.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            </div>
        </div>
</section>

<script>
  // Gunakan DOMContentLoaded untuk memastikan semua elemen HTML sudah dimuat
  document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi DataTables pada tabel dengan ID 'reportTable'
    if ($('#reportTable').length) {
        $("#reportTable").DataTable({
            // Aktifkan tombol-tombol ekspor
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
            "responsive": true, 
            "lengthChange": true, // Biarkan pengguna mengubah jumlah entri per halaman
            "autoWidth": false,
            // Pindahkan container tombol ke tempat yang benar
        }).buttons().container().appendTo('#reportTable_wrapper .col-md-6:eq(0)');
    }
  });
</script>