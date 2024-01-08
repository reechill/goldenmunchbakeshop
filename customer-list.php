<?php
require_once 'code/session-start.php';
require_once 'code/database-connect.php';
$title = 'Customer List';

// Redirect if not admin
if (!isset($_SESSION['adminid'])) {
    echo "<script> alert('Unauthorized access.'); window.location.href = 'index.php'; </script>";
    exit();
}

$sql = "SELECT * FROM customer ORDER BY name ASC";
$result = mysqli_query($conn, $sql);

require_once 'template/header.php';
?>

<div class="container mt-5">
    <h1 class="text-center my-5">Customer List</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Contact Number</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td><?= htmlspecialchars($row['username']); ?></td>
                    <td><?= htmlspecialchars($row['contactnumber']); ?></td>
                    <td><?= htmlspecialchars($row['address']); ?></td>
                    <td>
                        <a href="customer-edit.php?id=<?= $row['customerid']; ?>" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
require_once 'template/footer.php';
require_once 'code/database-close.php';
?>
