<?php
session_start();

// Koneksi ke database
$host = 'localhost';
$dbname = 'perpustakaan_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Proses form pendaftaran
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $gender = trim($_POST['gender']);
    $address = trim($_POST['address']);
    $contact = trim($_POST['contact']);
    $type = trim($_POST['type']);

    // Validasi input
    if (empty($username) || empty($password) || empty($confirm_password) || empty($firstname) || empty($lastname) || empty($email) || empty($gender) || empty($address) || empty($contact) || empty($type)) {
        header("Location: register.php?error=empty");
        exit();
    }

    // Validasi format email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: register.php?error=email_invalid");
        exit();
    }

    // Validasi password
    if ($password !== $confirm_password) {
        header("Location: register.php?error=password_mismatch");
        exit();
    }

    // Cek apakah username sudah digunakan
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        header("Location: register.php?error=username_taken");
        exit();
    }

    // Cek apakah email sudah digunakan
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM member WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        header("Location: register.php?error=email_taken");
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Mulai transaksi untuk memastikan data tersimpan di kedua tabel
        $pdo->beginTransaction();

        // Simpan ke tabel users
        $stmt = $pdo->prepare("INSERT INTO users (username, password, firstname, lastname) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $hashed_password, $firstname, $lastname]);
        $user_id = $pdo->lastInsertId();

        // Simpan ke tabel member
        $stmt = $pdo->prepare("INSERT INTO member (firstname, lastname, email, gender, address, contact, type, status) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([$firstname, $lastname, $email, $gender, $address, $contact, $type]);

        $pdo->commit();
        header("Location: login.php?success=registered");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        header("Location: register.php?error=failed");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Digital Library</title>
    
    <link rel="stylesheet" href="style.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <form action="register.php" method="post">
                <h2>Daftar Akun</h2>
                <p class="subtitle">Isi data untuk membuat akun baru</p>

                <?php
                // Tampilkan pesan error
                if (isset($_GET['error'])) {
                    $message = 'Terjadi kesalahan saat pendaftaran!';
                    if ($_GET['error'] == 'empty') {
                        $message = 'Semua kolom harus diisi!';
                    } elseif ($_GET['error'] == 'username_taken') {
                        $message = 'Username sudah digunakan!';
                    } elseif ($_GET['error'] == 'email_taken') {
                        $message = 'Email sudah digunakan!';
                    } elseif ($_GET['error'] == 'email_invalid') {
                        $message = 'Format email tidak valid!';
                    } elseif ($_GET['error'] == 'password_mismatch') {
                        $message = 'Password tidak cocok!';
                    }
                    echo '<div class="error-message">' . htmlspecialchars($message) . '</div>';
                }
                ?>

                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required>
                </div>

                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="firstname" placeholder="Nama Depan" required>
                </div>

                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="lastname" placeholder="Nama Belakang" required>
                </div>

                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email" required>
                </div>

                <div class="input-group">
                    <i class="fas fa-venus-mars"></i>
                    <select name="gender" required>
                        <option value="" disabled selected>Jenis Kelamin</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>

                <div class="input-group">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" name="address" placeholder="Alamat" required>
                </div>

                <div class="input-group">
                    <i class="fas fa-phone"></i>
                    <input type="text" name="contact" placeholder="Kontak" required>
                </div>

                <div class="input-group">
                    <i class="fas fa-user-tag"></i>
                    <select name="type" required>
                        <option value="" disabled selected>Tipe Member</option>
                        <option value="Regular">Regular</option>
                        <option value="Premium">Premium</option>
                    </select>
                </div>

                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
                </div>

                <button type="submit" name="submit" class="login-btn">Daftar</button>

                <div class="register-link">
                    <p>Sudah punya akun? <a href="login.php">Login sekarang</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>