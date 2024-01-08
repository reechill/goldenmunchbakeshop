<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $orderDetailId = $_POST['orderDetailId'] ?? null;
    $rating = $_POST['rating'] ?? null;
    $orderId = $_POST['orderId'] ?? null;

    if (is_null($orderDetailId) || is_null($rating)) {
        // Handle error - Redirect or show an error message
        header('Location: ../order-view.php?id=' . $orderId); // Adjust redirection as needed
        exit;
    }

    mysqli_begin_transaction($conn);

    try {
        // Update rating in orderdetailreadymade
        $updateRatingQuery = "UPDATE orderdetailreadymade SET rating = ? WHERE orderdetailreadymadeid = ?";
        $stmt = mysqli_prepare($conn, $updateRatingQuery);
        mysqli_stmt_bind_param($stmt, 'ii', $rating, $orderDetailId);
        mysqli_stmt_execute($stmt);

        // Retrieve product ID
        $productQuery = "SELECT productreadymadeid FROM orderdetailreadymade WHERE orderdetailreadymadeid = ?";
        $stmt = mysqli_prepare($conn, $productQuery);
        mysqli_stmt_bind_param($stmt, 'i', $orderDetailId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $productId = $row['productreadymadeid'];

        // Update average rating in productreadymade
        $updateProductRatingQuery = "UPDATE productreadymade SET rating = (SELECT AVG(rating) FROM orderdetailreadymade WHERE productreadymadeid = ?) WHERE productreadymadeid = ?";
        $stmt = mysqli_prepare($conn, $updateProductRatingQuery);
        mysqli_stmt_bind_param($stmt, 'ii', $productId, $productId);
        mysqli_stmt_execute($stmt);

        mysqli_commit($conn);

        // Redirect back to order view or another appropriate page
        header('Location: ../order-view.php?id=' . $orderId); // Adjust redirection as needed
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        // Handle error - Redirect or show an error message
        header('Location: ../order-view.php?id=' . $orderId); // Adjust redirection as needed
        exit;
    }
} else {
    // Redirect or show an error if not a POST request
    header('Location: ../index.php'); // Adjust redirection as needed
    exit;
}
?>
