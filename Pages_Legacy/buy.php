<?php

include("../Includes/database.php");
session_start();

//not logged in, gets redirected
if (!isset($_SESSION["user_id"])) {

    $_SESSION["redirect_after_login"] = "buy.php?product_id" . $_GET["product_id"] .
        "&seller_id=" . $_GET["seller_id"];

    header("Location: login.php");
    exit();
}

//Gets values
$product_id = intval($_GET["product_id"]);
$seller_id = intval($_GET["seller_id"]);
$buyer_id = $_SESSION["user_id"];

//Insert order
$sql = "INSERT INTO orders (product_id, buyer_id, seller_id, status)
            VALUES ('$product_id', '$buyer_id', '$seller_id', 'Pending' )";

if ($conn->query($sql)) {
    echo "Order placed successfully";
    echo "<br><a href='products.php'>Back to Marketplace</a>";
} else {
    echo "Error: " . $conn->error;
}
