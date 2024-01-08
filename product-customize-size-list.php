<?php 
    require_once 'code/session-start.php'; 
    require_once 'code/database-connect.php';

    $title = 'Size List';
    require_once 'template/header.php';

    // Fetch sizes
    $query = "SELECT sizeid, name, description, price, imagefilename FROM size ORDER BY name";
    $result = mysqli_query($conn, $query);
?>

<div class="container mt-4">
    <h1 class="text-center my-5">Size List</h1>
    <a href="product-customize-size-add.php" class="btn btn-success mb-3">Add New Size</a>
    <table class="table table-striped">
        <thead>
            <tr>
                
                <th scope="col">Name</th>
                <th scope="col">Description</th>
                <th scope="col" class="text-end">Price</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                if(mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        
                        
                        
                        echo "<td>".htmlspecialchars($row['name'])."</td>";
                        echo "<td>".htmlspecialchars($row['description'])."</td>";
                        echo "<td class='text-end'>".htmlspecialchars(number_format($row['price'], 2))."</td>";
                        echo "<td>";
                        echo "<a href='product-customize-size-edit.php?id=".$row['sizeid']."' class='btn btn-secondary btn-sm'>Edit</a> ";
                        echo "<a href='code/product-customize-size-delete.php?id=".$row['sizeid']."' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this size?\");'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No sizes found</td></tr>";
                }
            ?>
        </tbody>
    </table>
</div>

<?php 
    require_once 'code/database-close.php';
    require_once 'template/footer.php'; 
?>
