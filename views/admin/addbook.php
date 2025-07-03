<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Tambah Buku Baru</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="admin.php?p=listbook">Data Buku</a></li>
                    <li class="breadcrumb-item active">Tambah Buku</li>
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
                        <h3 class="card-title">Form Data Buku</h3>
                    </div>
                    <form method="post" action="action/book_save.php">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="book_title">Judul Buku</label>
                                        <input type="text" class="form-control" id="book_title" name="book_title" placeholder="Masukkan Judul Buku" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="author">Penulis</label>
                                        <input type="text" class="form-control" id="author" name="author" placeholder="Masukkan Nama Penulis" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="category">Kategori</label>
                                        <input type="text" class="form-control" id="category" name="category" placeholder="Contoh: Fiksi, Komputer, Sejarah">
                                    </div>
                                    <div class="form-group">
                                        <label for="isbn">ISBN</label>
                                        <input type="text" class="form-control" id="isbn" name="isbn" placeholder="Masukkan nomor ISBN">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="publisher_name">Penerbit</label>
                                        <input type="text" class="form-control" id="publisher_name" name="publisher_name" placeholder="Masukkan Nama Penerbit">
                                    </div>
                                    <div class="form-group">
                                        <label for="copyright_year">Tahun Terbit</label>
                                        <input type="number" class="form-control" id="copyright_year" name="copyright_year" placeholder="Contoh: 2024" min="1000">
                                    </div>
                                    <div class="form-group">
                                        <label for="book_copies">Jumlah Salinan</label>
                                        <input type="number" class="form-control" id="book_copies" name="book_copies" placeholder="Masukkan Jumlah Salinan" required min="0">
                                    </div>
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" name="status">
                                            <option value="1" selected>Tersedia</option>
                                            <option value="0">Tidak Tersedia</option>
                                        </select>
                                    </div>
                                </div>
                                </div>
                            </div>
                        <div class="card-footer">
                            <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                            <a href="admin.php?p=listbook" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
                </div>
        </div>
        </div></section>