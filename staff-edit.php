<?php 
require_once 'code/session-start.php';
require_once 'code/database-connect.php';

// Check if the staff ID is set in the URL
if (!isset($_GET['id'])) {
    echo "<script>alert('No staff ID provided.'); window.history.back();</script>";
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch staff data from the database
$sql = "SELECT * FROM staff WHERE staffid = '$id'";
$result = mysqli_query($conn, $sql);
if (!$result || mysqli_num_rows($result) !== 1) {
    echo "<script>alert('Staff not found.'); window.history.back();</script>";
    exit;
}

$staff = mysqli_fetch_assoc($result);

$title = 'Edit Staff';
require_once 'template/header.php'; 
?>

<div class="row">
    <div class="col-lg-4 m-auto">
        <h1 class="text-center my-5">Edit Staff</h1>
        <form action="code/staff-edit.php" method="POST">
            <input type="hidden" name="id" value="<?= $staff['staffid']; ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required value="<?= $staff['name']; ?>">
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required value="<?= $staff['username']; ?>">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">New Password (leave blank if you do not want to change it):</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <button type="submit" class="btn btn-primary w-100" name="btnUpdateStaff">Update Staff</button>
        </form>
    </div>
</div>

<?php require_once 'template/footer.php'; ?>
