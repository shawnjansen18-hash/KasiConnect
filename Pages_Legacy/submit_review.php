<?php
session_start();
include("../Includes/database.php");

//user must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

//gets form data
$product_id = intval($_POST["product_id"]);
$rating = intval($_POST["rating"]);
$review = $_POST["review"];
$user_id = $_SESSION["user_id"];

if (!$rating) {
    die("Please select a rating");
}

//insert review
$sql = "INSERT INTO reviews(product_id, user_id, rating, review)
            VALUES('$product_id', '$user_id','$rating','$review')";

if ($conn->query($sql)) {
    header("Location: product_details.php?id=$product_id");
    exit();
} else {
    echo "Error: " . $conn->error;
}
