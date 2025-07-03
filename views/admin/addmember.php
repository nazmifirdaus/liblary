<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Tambah Anggota Baru</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="admin.php?p=listmember">Data Anggota</a></li>
                    <li class="breadcrumb-item active">Tambah Anggota</li>
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
                        <h3 class="card-title">Form Tambah Anggota</h3>
                    </div>
                    <form method="post" action="action/member_save.php">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname">Nama Depan</label>
                                        <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Masukkan nama depan" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email yang valid" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="gender">Jenis Kelamin</label>
                                        <select class="form-control" id="gender" name="gender">
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lastname">Nama Belakang</label>
                                        <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Masukkan nama belakang" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="contact">No Telp</label>
                                        <input type="tel" class="form-control" id="contact" name="contact" placeholder="Contoh: 08123456789">
                                    </div>
                                    <div class="form-group">
                                        <label for="type">Tipe Anggota</label>
                                        <select class="form-control" id="type" name="type">
                                            <option value="Guru">Guru</option>
                                            <option value="Siswa">Siswa</option>
                                        </select>
                                    </div>
                                </div>
                                </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address">Alamat</label>
                                        <textarea class="form-control" id="address" name="address" placeholder="Masukkan alamat lengkap"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                            <a href="admin.php?p=listmember" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
                </div>
            </div>
        </div></section>