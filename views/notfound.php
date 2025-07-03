<?php
// Start session (if authentication is required)
session_start();

// Database connection
$host = 'localhost';
$dbname = 'perpustakaan_db';
$username = 'root'; // Default XAMPP username
$password = '';     // Default XAMPP password (empty)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if user is logged in (basic authentication check)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle page parameter
$page = isset($_GET['p']) ? $_GET['p'] : 'dashboard';

if ($page === 'listtransaksi') {
    // Query to fetch transaction data
    $stmt = $pdo->prepare("
        SELECT 
            b.borrow_id,
            m.firstname,
            m.lastname,
            bk.book_title,
            b.date_borrow,
            b.due_date,
            bd.borrow_status,
            bd.date_return
        FROM borrow b
        JOIN member m ON b.member_id = m.member_id
        JOIN borrowdetails bd ON b.borrow_id = bd.borrow_id
        JOIN book bk ON bd.book_id = bk.book_id
        ORDER BY b.date_borrow DESC
    ");
    $stmt->execute();
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Library - Transaction List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            color: white;
            padding: 15px;
            text-decoration: none;
            display: block;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center text-white">Digital Library</h4>
        <a href="?p=dashboard">Dashboard</a>
        <a href="?p=manajemendata">Manajemen Data</a>
        <a href="?p=listtransaksi" class="active">Transaksi</a>
        <a href="?p=laporanggota">Laporan Anggota</a>
        <a href="?p=logout" class="text-danger">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h2>Transaction List</h2>
        <?php if ($page === 'listtransaksi' && !empty($transactions)): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Member Name</th>
                        <th>Book Title</th>
                        <th>Borrow Date</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Return Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($transaction['borrow_id']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['firstname'] . ' ' . $transaction['lastname']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['book_title']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['date_borrow']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['due_date']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['borrow_status']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['date_return'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($page === 'listtransaksi'): ?>
            <p>No transactions found.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>