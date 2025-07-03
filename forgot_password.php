<?php
session_start();

// Buat token CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Digital Library</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <form action="process_forgot_password.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <h2>Lupa Password</h2>
                <p class="subtitle">Masukkan email untuk mereset password</p>

                <?php
                // Tampilkan pesan error
                if (!empty($_SESSION['error_messages']) && is_array($_SESSION['error_messages'])) {
                    foreach ($_SESSION['error_messages'] as $error) {
                        echo '<div class="error-message">' . htmlspecialchars($error) . '</div>';
                    }
                    $_SESSION['error_messages'] = [];
                }
                // Tampilkan pesan sukses
                if (!empty($_SESSION['success_message'])) {
                    echo '<div class="success-message">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
                    $_SESSION['success_message'] = '';
                }
                ?>

                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <button type="submit" name="submit" class="login-btn">Kirim</button>

                <div class="register-link">
                    <p>Kembali ke <a href="index.php">Login</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>