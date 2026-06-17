<?php
session_start();
include("../Includes/database.php");

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$id = intval($_GET["id"]);
$user_id = $_SESSION["user_id"];

$sql = "DELETE FROM products  WHERE id = '$id' AND user_id = '$user_id'";
$conn->query($sql);

header("Location: my_products.php");
exit();
