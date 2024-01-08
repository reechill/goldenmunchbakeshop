<?php require_once 'code/session-start.php'; ?>
<?php require_once 'code/database-connect.php'; ?>

<?php $title = 'Staff List'; ?>

<?php require_once 'template/header.php'; ?>

<div class="row">
    <div class="col-lg-12 m-auto">
        <h1 class="text-center my-5">Staff List</h1>
        <a href="staff-add.php" class="btn btn-success mb-3">Add New Staff</a>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Username</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch staff from the database
                $sql = "SELECT * FROM staff";
                $result = mysqli_query($conn, $sql);
                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<th scope='row'>" . $row['staffid'] . "</th>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['username'] . "</td>";
                        echo "<td>";
                        echo "<a href='staff-edit.php?id=" . $row['staffid'] . "' class='btn btn-primary'>Edit</a> ";
                        echo "<a href='code/staff-delete.php?id=" . $row['staffid'] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this staff?\");'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'template/footer.php'; ?>
