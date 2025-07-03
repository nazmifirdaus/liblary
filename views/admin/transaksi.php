<?php
// Database queries for transaction data
$stmt_borrowed = $pdo->prepare("
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
    WHERE bd.borrow_status = 'Borrowed'
    ORDER BY b.date_borrow DESC
");
$stmt_borrowed->execute();
$borrowed_books = $stmt_borrowed->fetchAll(PDO::FETCH_ASSOC);

$stmt_available = $pdo->prepare("
    SELECT 
        book_id,
        book_title,
        author,
        book_copies,
        status
    FROM book
    WHERE status = 'Available'
    ORDER BY book_title
");
$stmt_available->execute();
$available_books = $stmt_available->fetchAll(PDO::FETCH_ASSOC);

$stmt_total = $pdo->query("SELECT SUM(book_copies) as total_stock FROM book");
$total_stock = $stmt_total->fetch(PDO::FETCH_ASSOC)['total_stock'] ?? 0;
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo htmlspecialchars($page_title); ?></h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Borrowed Books -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Borrowed Books</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($borrowed_books)): ?>
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Member Name</th>
                                <th>Book Title</th>
                                <th>Borrow Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($borrowed_books as $book): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($book['borrow_id']); ?></td>
                                    <td><?php echo htmlspecialchars($book['firstname'] . ' ' . $book['lastname']); ?></td>
                                    <td><?php echo htmlspecialchars($book['book_title']); ?></td>
                                    <td><?php echo htmlspecialchars($book['date_borrow']); ?></td>
                                    <td><?php echo htmlspecialchars($book['due_date']); ?></td>
                                    <td><?php echo htmlspecialchars($book['borrow_status']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No books currently borrowed.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Available Books -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Available Books</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($available_books)): ?>
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Book ID</th>
                                <th>Book Title</th>
                                <th>Author</th>
                                <th>Stock</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($available_books as $book): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($book['book_id']); ?></td>
                                    <td><?php echo htmlspecialchars($book['book_title']); ?></td>
                                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                                    <td><?php echo htmlspecialchars($book['book_copies']); ?></td>
                                    <td><?php echo htmlspecialchars($book['status']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No books currently available.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Total Stock Summary -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Stock Summary</h3>
            </div>
            <div class="card-body">
                <p>Total available copies across all books: <?php echo htmlspecialchars($total_stock); ?></p>
            </div>
        </div>
    </div>
</section>