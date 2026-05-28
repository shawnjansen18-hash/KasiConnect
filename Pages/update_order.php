<?php
include("../Includes/database.php");
session_start();

if (!isset($_session["USER_ID"])) {
    HEADER("Location: login.php");
    exit();
}

$order_id = intval($_GET["id"]);
$status = $_GET["status"];
$seller_id = $_SESSION["user_id"];

//security: ensure  sellerowns the order
$sql = "SELECT * FROM orders WHERE id = $order_id AND seller_id = $seller_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $update = "UPDATE orders SET status = '$status' WHERE id = $order_id";

    if ($conn->query($update)) {
        header("Location: seller_dashboard.php");
        exit();
    } else {
        echo "Error updating order";
    }
}
