<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=not_logged_in");
    exit();
}

// Koneksi ke database
$host = 'localhost';
$dbname = 'digital_library';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Ambil data pengguna
$stmt = $pdo->prepare("SELECT u.user_id, u.username, u.firstname, u.lastname, m.email, m.gender, m.address, m.contact, m.type 
                       FROM users u 
                       JOIN member m ON u.firstname = m.firstname AND u.lastname = m.lastname 
                       WHERE u.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil riwayat peminjaman
$stmt = $pdo->prepare("SELECT b.borrow_id, b.date_borrow, b.due_date, b.status, bd.book_id, bk.book_title, bd.date_return 
                       FROM borrow b 
                       JOIN borrowdetails bd ON b.borrow_id = bd.borrow_id 
                       JOIN book bk ON bd.book_id = bk.book_id 
                       WHERE b.member_id = (SELECT member_id FROM member WHERE email = ?)");
$stmt->execute([$user['email']]);
$borrows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Member - Digital Library</title>
    
    <link rel="stylesheet" href="style.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <nav class="sidebar">
            <h3>Digital Library</h3>
            <ul>
                <li><a href="member.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="books.php"><i class="fas fa-book"></i> Koleksi Buku</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> Profil</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
        <div class="main-content">
            <header>
                <h2>Selamat Datang, <?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?>!</h2>
                <p>Dashboard Member</p>
            </header>
            <div class="content">
                <div class="card">
                    <h3>Informasi Akun</h3>
                    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                    <p>Jenis Kelamin: <?php echo htmlspecialchars($user['gender']); ?></p>
                    <p>Alamat: <?php echo htmlspecialchars($user['address']); ?></p>
                    <p>Kontak: <?php echo htmlspecialchars($user['contact']); ?></p>
                    <p>Tipe: <?php echo htmlspecialchars($user['type']); ?></p>
                </div>
                <div class="card">
                    <h3>Riwayat Peminjaman</h3>
                    <?php if (empty($borrows)): ?>
                        <p>Belum ada riwayat peminjaman.</p>
                    <?php else: ?>
                        <table class="borrow-table">
                            <thead>
                                <tr>
                                    <th>Judul Buku</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Tanggal Kembali</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($borrows as $borrow): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($borrow['book_title']); ?></td>
                                        <td><?php echo htmlspecialchars($borrow['date_borrow']); ?></td>
                                        <td><?php echo htmlspecialchars($borrow['due_date']); ?></td>
                                        <td><?php echo htmlspecialchars($borrow['date_return'] ?? 'Belum Dikembalikan'); ?></td>
                                        <td><?php echo $borrow['status'] == 1 ? 'Dipinjam' : 'Selesai'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>