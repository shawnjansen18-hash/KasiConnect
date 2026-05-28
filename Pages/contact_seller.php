<?php
include("../Includes/database.php");

$product_id = $_GET["product_id"];
$product = $_GET["seller"];

?>

<?php include("../Includes/header.php") ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<h2>contact seller</h2>

<body>

    <p><strong>Seller:</strong><? $seller ?> <!--find out what does-->

        <hr>

    <form method="post">
        <textarea name="message" placeholder="Type your message..." required></textarea><br><br>
        <button type=submit>Send Message</button>

    </form>
</body>

</html>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $_POST["message"];
    $user = $_SESSION["user"] ?? "Guest";
    echo "<p><strong>Message sent:</strong></p>";
}
?>

<?php include("../Includes/footer.php") ?>