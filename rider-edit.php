<?php
require_once 'code/session-start.php';
require_once 'code/database-connect.php';

// Get rider details
if (isset($_GET['riderid'])) {
    $riderid = mysqli_real_escape_string($conn, $_GET['riderid']);
    $query = "SELECT * FROM rider WHERE riderid = $riderid";
    $result = mysqli_query($conn, $query);
    $rider = mysqli_fetch_assoc($result);
}

$title = 'Update Rider';
require_once 'template/header.php';
?>

<div class="row">
    <div class="col-lg-6 m-auto">
        <h1 class="text-center my-5">Update Rider</h1>
        <form action="code/rider-edit.php" method="POST">
            <input type="hidden" name="riderid" value="<?= htmlspecialchars($rider['riderid']); ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($rider['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="contactnumber" class="form-label">Contact Number:</label>
                <input type="text" class="form-control" id="contactnumber" name="contactnumber" value="<?= htmlspecialchars($rider['contactnumber']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($rider['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">New Password (leave blank if not changing):</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <button type="submit" class="btn btn-primary w-100" name="btnUpdateRider">Update Rider</button>
        </form>
    </div>
</div>

<?php require_once 'template/footer.php'; ?>
