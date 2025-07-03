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

// Buat token CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Proses token dari URL
$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$email = '';

if ($token) {
    // Cek token di database
    $stmt = $pdo->prepare("SELECT email, expires_at FROM password_resets WHERE token = ?");
    $stmt->execute([$token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($reset && strtotime($reset['expires_at']) > time()) {
        $email = $reset['email'];
    } else {
        $_SESSION['error_messages'] = ['Token tidak valid atau sudah kedaluwarsa!'];
        header("Location: forgot_password.php");
        exit();
    }
}

// Proses form reset password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_messages'] = ['Token tidak valid!'];
        header("Location: reset_password.php?token=$token");
        exit();
    }

    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validasi input
    if (empty($password) || empty($confirm_password)) {
        $_SESSION['error_messages'] = ['Password tidak boleh kosong!'];
        header("Location: reset_password.php?token=$token");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error_messages'] = ['Password dan konfirmasi password tidak cocok!'];
        header("Location: reset_password.php?token=$token");
        exit();
    }

    // Update password di tabel users
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users u 
                           JOIN member m ON u.user_id = m.user_id 
                           SET u.password = ? 
                           WHERE m.email = ?");
    $stmt->execute([$hashed_password, $email]);

    // Hapus token dari database
    $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
    $stmt->execute([$email]);

    $_SESSION['success_message'] = 'Password berhasil direset! Silakan login.';
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Digital Library</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <h2>Reset Password</h2>
                <p class="subtitle">Masukkan password baru Anda</p>

                <?php
                // Tampilkan pesan error
                if (!empty($_SESSION['error_messages']) && is_array($_SESSION['error_messages'])) {
                    foreach ($_SESSION['error_messages'] as $error) {
                        echo '<div class="error-message">' . htmlspecialchars($error) . '</div>';
                    }
                    $_SESSION['error_messages'] = [];
                }
                ?>

                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password Baru" required>
                </div>

                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
                </div>

                <button type="submit" name="submit" class="login-btn">Reset Password</button>

                <div class="register-link">
                    <p>Kembali ke <a href="login.php">Login</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>