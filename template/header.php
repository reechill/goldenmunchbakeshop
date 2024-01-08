<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

        <link rel="icon" href="img/goldenmunchbakeshop.webp" type="image/webp">

        <style>
        /* Add the following style to change the color to gold */
        .navbar-brand {
            color: black !important;
        }

        
        /* Add the following style to change the background color to yellow */
        #navbar {
            background-color: #ffd700 !important;
    }

    </style>

    
        <title><?= $title; ?></title>
    </head>
    <body>
        
        <?php
        require_once 'code/session-start.php';
        require_once 'code/database-connect.php';

        $totalQuantity = 0;
        if (isset($_SESSION['customerid'])) {
            $customerId = $_SESSION['customerid'];

            // Query to get the total quantity in the ready-made cart
            $readyMadeCartQuery = "SELECT SUM(quantity) as totalQuantity FROM cartproductreadymade WHERE customerid = $customerId";
            $readyMadeCartResult = mysqli_query($conn, $readyMadeCartQuery);
            if ($readyMadeCartResult) {
                $cartRow = mysqli_fetch_assoc($readyMadeCartResult);
                $totalQuantity += $cartRow['totalQuantity'] ?? 0;
            }

            // Query to get the total quantity in the customized cart
            $customizedCartQuery = "SELECT SUM(quantity) as totalQuantity FROM cartproductcustomized WHERE customerid = $customerId";
            $customizedCartResult = mysqli_query($conn, $customizedCartQuery);
            if ($customizedCartResult) {
                $cartRow = mysqli_fetch_assoc($customizedCartResult);
                $totalQuantity += $cartRow['totalQuantity'] ?? 0;
            }
        }
        ?>
        
        <nav class="navbar navbar-expand-lg bg-body-tertiary d-print-none" id="navbar">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">
                    <img src="img/goldenmunchbakeshop.webp" alt="Logo" style="height:70px; margin-right:10px;">Golden Munch Bakeshop
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarText">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <?php if ( isset($_SESSION['adminid']) || isset($_SESSION['staffid']) || isset($_SESSION['customerid']) || isset($_SESSION['riderid']) ) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="order-list.php">Orders</a>
                            </li>
                        <?php endif; ?>
                        <?php if ( isset($_SESSION['adminid']) ) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="customer-list.php">Customers</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="staff-list.php">Staff</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="rider-list.php">Riders</a>
                        </li>
                        <?php endif; ?>
                        <?php if ( isset($_SESSION['adminid']) || isset($_SESSION['staffid']) ) : ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Products
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="category-list.php">Categories</a></li>
                                <li><a class="dropdown-item" href="product-readymade-list.php">Ready-Made</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="product-customize-flavor-list.php">Flavor</a></li>
                                <li><a class="dropdown-item" href="product-customize-shape-list.php">Shape</a></li>
                                <li><a class="dropdown-item" href="product-customize-size-list.php">Size</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="report.php">Sales</a>
                        </li>
                        <?php endif; ?>
                        <?php if ( isset($_SESSION['customerid']) ) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="customer-edit.php">My Profile</a>
                        </li>
                        <?php endif; ?>
                        <?php if ( !isset($_SESSION['riderid']) ) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="cart-readymade-list.php">Ready-Made Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="cart-customized-list.php">Customized Products</a>
                        </li>
                        <?php endif; ?>
                        <?php if ( isset($_SESSION['customerid']) ) : ?>
                        <li class="nav-item">
                            
                        </li>
                        <li class="nav-item">
                            <?php if ($totalQuantity > 0): ?>
                                <a class="nav-link" href="order-checkout.php">Checkout (<?= $totalQuantity ?>)</a>
                            <?php else: ?>
                                <span class="nav-link disabled">Checkout (0)</span>
                            <?php endif; ?>
                        </li>
                        <?php endif; ?>
                    </ul>
                    <span class="navbar-text">
                        <?php if (isset($_SESSION['name'])): ?>
                            <?= "Logged in as: " . ucfirst(htmlspecialchars($_SESSION['name'])); ?>
                            <?php if (isset($_SESSION['adminid'])): ?>
                                (Admin)
                            <?php elseif (isset($_SESSION['staffid'])): ?>
                                (Staff)
                            <?php elseif (isset($_SESSION['riderid'])): ?>
                                (Rider)
                            <?php endif; ?>
                            <a href="code/logout.php" class="ms-3">Logout</a>
                        <?php else: ?>
                            <a href="index.php" class="ms-3">Login</a>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </nav>
        
        <div class="container p-5">