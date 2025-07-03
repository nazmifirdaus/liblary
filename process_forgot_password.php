<?php
session_start();

// PHPMailer namespace imports
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Koneksi ke database
$host = 'localhost';
$dbname = 'perpustakaan_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $_SESSION['error_messages'] = ['Koneksi gagal: ' . $e->getMessage()];
    header("Location: forgot_password.php");
    exit();
}

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    // Validasi CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_messages'] = ['Token tidak valid!'];
        header("Location: forgot_password.php");
        exit();
    }

    $email = trim($_POST['email']);

    // Validasi input
    if (empty($email)) {
        $_SESSION['error_messages'] = ['Email tidak boleh kosong!'];
        header("Location: forgot_password.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_messages'] = ['Format email tidak valid!'];
        header("Location: forgot_password.php");
        exit();
    }

    // Cek apakah email ada di database
    try {
        $stmt = $pdo->prepare("SELECT u.user_id, u.username, u.firstname, u.lastname, m.email 
                               FROM users u 
                               JOIN member m ON u.user_id = m.user_id 
                               WHERE m.email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $_SESSION['error_messages'] = ['Error database: ' . $e->getMessage()];
        header("Location: forgot_password.php");
        exit();
    }

    if ($user) {
        // Generate token reset
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Hapus token lama
        try {
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
            $stmt->execute([$email]);
        } catch (PDOException $e) {
            $_SESSION['error_messages'] = ['Gagal menghapus token lama: ' . $e->getMessage()];
            header("Location: forgot_password.php");
            exit();
        }

        // Simpan token baru
        try {
            $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$email, $token, $expires]);
        } catch (PDOException $e) {
            $_SESSION['error_messages'] = ['Gagal menyimpan token: ' . $e->getMessage()];
            header("Location: forgot_password.php");
            exit();
        }

        // Kirim email dengan PHPMailer
        try {
            if (!file_exists('vendor/autoload.php')) {
                throw new Exception('PHPMailer autoload file tidak ditemukan. Pastikan Composer telah dijalankan.');
            }
            require 'vendor/autoload.php';

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your_email@gmail.com'; // Ganti dengan email Anda
            $mail->Password = 'your_app_password'; // Ganti dengan App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom('your_email@gmail.com', 'Digital Library');
            $mail->addAddress($email, $user['firstname'] . ' ' . $user['lastname']);
            $mail->isHTML(true);
            $mail->Subject = 'Reset Password - Digital Library';
            $reset_link = "http://localhost/Library/reset_password.php?token=" . $token;
            $mail->Body = "Halo {$user['firstname']},<br><br>
                           Klik link berikut untuk mereset password Anda: <a href='$reset_link'>Reset Password</a><br>
                           Link ini berlaku selama 1 jam.<br><br>
                           Jika Anda tidak meminta reset password, abaikan email ini.<br>
                           Terima kasih,<br>Tim Digital Library";

            $mail->send();
            $_SESSION['success_message'] = 'Link reset password telah dikirim ke email Anda!';
            header("Location: forgot_password.php");
            exit();
        } catch (Exception $e) {
            // Fallback ke mail() untuk debugging
            $reset_link = "http://localhost/Library/reset_password.php?token=" . $token;
            $subject = "Reset Password - Digital Library";
            $message = "Halo {$user['firstname']},<br><br>
                        Klik link berikut untuk mereset password Anda: <a href='$reset_link'>Reset Password</a><br>
                        Link ini berlaku selama 1 jam.<br><br>
                        Jika Anda tidak meminta reset password, abaikan email ini.<br>
                        Terima kasih,<br>Tim Digital Library";
            $headers = "From: no-reply@localhost\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\n";
            if (mail($email, $subject, $message, $headers)) {
                $_SESSION['success_message'] = 'Link reset password telah dikirim ke email Anda!';
            } else {
                $_SESSION['error_messages'] = ['Gagal mengirim email: ' . $e->getMessage()];
            }
            header("Location: forgot_password.php");
            exit();
        }
    } else {
        $_SESSION['error_messages'] = ['Email tidak ditemukan!'];
        header("Location: forgot_password.php");
        exit();
    }
} else {
    header("Location: forgot_password.php");
    exit();
}
?>