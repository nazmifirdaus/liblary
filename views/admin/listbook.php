<?php
// Pastikan file koneksi ada di path yang benar
// Jika file ini berada di direktori yang sama dengan listmember.php, maka path ini seharusnya sudah benar.
include "koneksi.php";
?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Data Buku</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Data Buku</li>
                </ol>
            </div>
        </div>
    </div></section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <a href="admin.php?p=addbook" class="btn btn-primary">Tambah Data Buku</a>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Judul Buku</th>
                                    <th>Penulis</th>
                                    <th>Penerbit</th>
                                    <th>ISBN</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Query diubah untuk mengambil data dari tabel 'book'
                                $dml = "SELECT * FROM book ORDER BY book_title ASC";
                                $qry = mysqli_query($db, $dml);
                                $no = 1;

                                // Gunakan mysqli_fetch_assoc untuk efisiensi
                                while ($row = mysqli_fetch_assoc($qry)) {
                                    // ID diambil dari kolom 'book_id'
                                    $id = $row['book_id'];
                                ?>
                                    <tr>
                                        <td><?php echo $no; ?>.</td>
                                        <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                                        <td><?php echo htmlspecialchars($row['author']); ?></td>
                                        <td><?php echo htmlspecialchars($row['publisher_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['isbn']); ?></td>
                                        <td><?php echo htmlspecialchars($row['book_copies']); ?></td>
                                        <td><?php echo $row['status'] == '1' ? "Tersedia" : "Tidak Tersedia"; ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="admin.php?p=editbook&id=<?php echo $id; ?>" class="btn btn-info"><i class="fas fa-edit"></i></a>
                                                <a href="action/book_delete.php?id=<?php echo $id; ?>" class="btn btn-danger" onclick="return confirm('Anda yakin ingin menghapus buku ini?')"><i class="fas fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php $no++;
                                } ?>
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </section>