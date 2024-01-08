<?php 
    require_once 'code/session-start.php'; 
    require_once 'code/database-connect.php';

    $title = 'Ready-Made Product List';
    require_once 'template/header.php';

    // Fetch products and their categories
    $query = "SELECT p.productreadymadeid, p.name, p.description, p.price, p.imagefilename, p.isavailable, GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', ') AS categories 
              FROM productreadymade p 
              LEFT JOIN productcategory pc ON p.productreadymadeid = pc.productreadymadeid 
              LEFT JOIN category c ON pc.categoryid = c.categoryid 
              GROUP BY p.productreadymadeid 
              ORDER BY p.name";

    $result = mysqli_query($conn, $query);
?>

<div class="container mt-4">
    <h1 class="text-center my-5">Ready-Made Product List</h1>
    <a href="product-readymade-add.php" class="btn btn-success mb-3">Add New Product</a>
    <table class="table table-striped">
        <thead>
            <tr>
            <th scope="col">Image</th>
                <th scope="col">Name</th>
                <th scope="col">Description</th>
                <th scope="col" class="text-end">Price</th>
                <th scope="col">Categories</th>
                <th scope="col">Availability</th> <!-- New Column for Availability -->
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                if(mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $availabilityText = $row['isavailable'] == 'y' ? 'Available' : 'Not Available';
                        $toggleButtonText = $row['isavailable'] == 'y' ? 'Make Not Available' : 'Make Available';

                        echo "<tr>";
                        if (!empty($row['imagefilename'])) {
                            echo "<td><img src='img/upload/readymadeproducts/".$row['imagefilename']."' alt='".$row['name']."' style='height: 100px; width: 100px; object-fit: cover;'></td>";
                        } else {
                            // Display placeholder or leave blank
                            echo "<td><img src='img/no-image-available.png' alt='No image available' style='height: 100px; width: 100px; object-fit: cover;'></td>";
                        }
                        echo "<td>".htmlspecialchars($row['name'])."</td>";
                        echo "<td>".htmlspecialchars($row['description'])."</td>";
                        echo "<td class='text-end'>".htmlspecialchars(number_format($row['price'], 2))."</td>";
                        echo "<td>".htmlspecialchars($row['categories'])."</td>";
                        echo "<td>$availabilityText</td>"; 
                        echo "<td>";
                        echo "<a href='product-readymade-edit.php?id=".$row['productreadymadeid']."' class='btn btn-secondary btn-sm'>Edit</a> ";
                        echo "<a href='code/product-readymade-delete.php?id=".$row['productreadymadeid']."' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this product?\");'>Delete</a>";
                        echo "<a href='code/product-readymade-status.php?id=".$row['productreadymadeid']."' class='btn btn-sm ".($row['isavailable'] == 'y' ? 'btn-warning' : 'btn-info')."'>$toggleButtonText</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No products found</td></tr>";
                }
            ?>
        </tbody>
    </table>
</div>

<?php 
    require_once 'code/database-close.php';
    require_once 'template/footer.php'; 
?>
