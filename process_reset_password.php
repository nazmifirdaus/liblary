<?php
session_start();
include "koneksi.php";

if (isset($_POST['submit'])) {
    $token = trim($_POST['token']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validasi input
    if (empty($password) || empty($confirm_password)) {
        header("Location: reset_password.php?token=" . urlencode($token) . "&error=empty");
        exit();
    }
    
    if ($password !== $confirm_password) {
        header("Location: reset_password.php?token=" . urlencode($token) . "&error=mismatch");
        exit();
    }
    
    if (strlen($password) < 6) {
        header("Location: reset_password.php?token=" . urlencode($token) . "&error=short");
        exit();
    }
    
    // Validasi token
    $sql = "SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()";
    if ($stmt = mysqli_prepare($db, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $token);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $email = $row['email'];
            
            // Update password user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users u 
                          JOIN member m ON u.user_id = m.user_id 
                          SET u.password = ? 
                          WHERE m.email = ?";
            
            if ($update_stmt = mysqli_prepare($db, $update_sql)) {
                mysqli_stmt_bind_param($update_stmt, "ss", $hashed_password, $email);
                mysqli_stmt_execute($update_stmt);
                
                if (mysqli_stmt_affected_rows($update_stmt) > 0) {
                    // Hapus token setelah berhasil
                    $delete_sql = "DELETE FROM password_resets WHERE token = ?";
                    if ($delete_stmt = mysqli_prepare($db, $delete_sql)) {
                        mysqli_stmt_bind_param($delete_stmt, "s", $token);
                        mysqli_stmt_execute($delete_stmt);
                        mysqli_stmt_close($delete_stmt);
                    }
                    
                    mysqli_stmt_close($update_stmt);
                    header("Location: login.php?message=password_reset&type=success");
                    exit();
                } else {
                    mysqli_stmt_close($update_stmt);
                    header("Location: reset_password.php?token=" . urlencode($token) . "&error=failed");
                    exit();
                }
            }
        } else {
            header("Location: reset_password.php?token=" . urlencode($token) . "&error=invalid");
            exit();
        }
        
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($db);
} else {
    header("Location: login.php");
    exit();
}
?>