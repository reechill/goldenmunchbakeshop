<?php
require_once 'code/session-start.php';
require_once 'code/database-connect.php';

$title = 'Rider List';
require_once 'template/header.php';

// Fetch riders from the database
$result = mysqli_query($conn, "SELECT * FROM rider");
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="text-center my-5">Rider List</h1>
        <a href="rider-add.php" class="btn btn-success mb-3">Add New Rider</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Contact Number</th>
                    <th scope="col">Username</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($rider = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <th scope="row"><?= htmlspecialchars($rider['riderid']); ?></th>
                        <td><?= htmlspecialchars($rider['name']); ?></td>
                        <td><?= htmlspecialchars($rider['contactnumber']); ?></td>
                        <td><?= htmlspecialchars($rider['username']); ?></td>
                        <td>
                            <a href="rider-edit.php?riderid=<?= $rider['riderid']; ?>" class="btn btn-primary">Edit</a>
                            <a href="code/rider-delete.php?riderid=<?= $rider['riderid']; ?>" class="btn btn-danger" onclick='return confirm("Are you sure you want to delete this rider?");'>Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once 'template/footer.php';
require_once 'code/database-close.php';
