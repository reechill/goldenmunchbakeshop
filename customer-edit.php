<?php
require_once 'code/session-start.php';
require_once 'code/database-connect.php';
$title = 'Edit Profile';

$customerid = isset($_GET['id']) ? $_GET['id'] : $_SESSION['customerid'];
$isAdmin = isset($_SESSION['adminid']);
$isCustomer = isset($_SESSION['customerid']) && $_SESSION['customerid'] == $customerid;

if (!$isAdmin && !$isCustomer) {
    echo "<script> alert('Unauthorized access.'); window.location.href = 'customer-login.php'; </script>";
    exit();
}

$sql = "SELECT * FROM customer WHERE customerid='$customerid'";
$result = mysqli_query($conn, $sql);
if (!$result) {
    echo "<script> alert('Error fetching data: " . mysqli_error($conn) . "'); window.history.back(); </script>";
    exit();
}
$customerData = mysqli_fetch_assoc($result);

require_once 'template/header.php';
?>

<div class="row">
    <div class="col-lg-6 m-auto">
        <h1 class="text-center my-5">Edit Profile</h1>
        <form action="code/customer-edit.php" method="POST">
            <input type="hidden" name="customerid" value="<?= $customerid; ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($customerData['name']); ?>" <?= $isAdmin ? '' : 'readonly'; ?>>
                <div class="form-text">
                    For any changes in the name, please contact store admin.
                </div>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($customerData['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">New Password (leave blank if not changing):</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="mb-3">
                <label for="contactnumber" class="form-label">Contact Number:</label>
                <input type="text" class="form-control" id="contactnumber" name="contactnumber" value="<?= htmlspecialchars($customerData['contactnumber']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address:</label>
                <textarea class="form-control" id="address" name="address" <?= $isAdmin ? '' : 'readonly'; ?> rows="3"><?= htmlspecialchars($customerData['address']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100" name="btnUpdate">Update</button>
        </form>
    </div>
</div>

<?php
require_once 'template/footer.php';
require_once 'code/database-close.php';
?>
