<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Data Anggota</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Data Anggota</li>
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
                        <a href="admin.php?p=addmember" class="btn btn-primary">Tambah Data</a>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">No.</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Alamat</th>
                                    <th>Kontak</th>
                                    <th>Tipe</th>
                                    <th>Status</th>
                                    <th class="text-center" style="width: 10%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Sertakan koneksi di awal
                                include "koneksi.php";

                                // Query untuk mengambil data anggota
                                $dml = "SELECT * FROM member ORDER BY firstname ASC";
                                $qry = mysqli_query($db, $dml);

                                // 1. Tambahkan pengecekan jika query berhasil dieksekusi
                                if ($qry) {
                                    $no = 1;
                                    // 2. Tambahkan pengecekan jika ada data yang ditemukan
                                    if (mysqli_num_rows($qry) > 0) {
                                        // 3. Gunakan mysqli_fetch_assoc untuk efisiensi
                                        while ($row = mysqli_fetch_assoc($qry)) {
                                            $id = $row['member_id'];
                                ?>
                                            <tr>
                                                <td class="text-center"><?php echo $no . "."; ?></td>
                                                <td><?php echo htmlspecialchars($row['firstname'] . " " . $row['lastname']); ?></td>
                                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                                <td><?php echo $row['gender'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                                                <td><?php echo htmlspecialchars($row['address']); ?></td>
                                                <td><?php echo htmlspecialchars($row['contact']); ?></td>
                                                <td><?php echo htmlspecialchars($row['type']); ?></td>
                                                <td><?php echo $row['status'] == 1 ? "Aktif" : "Tidak Aktif"; ?></td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="admin.php?p=editmember&id=<?php echo $id; ?>" class="btn btn-info" title="Edit"><i class="fas fa-edit"></i></a>
                                                        <a href="action/member_delete.php?id=<?php echo $id; ?>" class="btn btn-danger" title="Hapus" onclick="return confirm('Anda yakin ingin menghapus data ini?')"><i class="fas fa-trash"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php
                                            $no++;
                                        }
                                    } else {
                                        // Tampilkan pesan jika tidak ada data
                                        echo '<tr><td colspan="9" class="text-center">Tidak ada data anggota ditemukan.</td></tr>';
                                    }
                                    // Bebaskan memori hasil query
                                    mysqli_free_result($qry);
                                } else {
                                    // Tampilkan pesan jika query gagal
                                    echo '<tr><td colspan="9" class="text-center">Error: Gagal mengeksekusi query. ' . mysqli_error($db) . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </section>