<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KasiConnect Marketplace</title>
    <link rel="stylesheet" href="/KasiConnect/CSS/style.css">
</head>

<body>

    <header class="navbar">

        <h1 class="logo">KasiConnect</h1>

        <nav>
            <a href="/KasiConnect/index.php">Home</a>

            <?php if (isset($_SESSION["user"])): ?>

                <a href="/KasiConnect/logout.php">Logout</a>
                <a href="/KasiConnect/Pages/products.php">Marketplace</a>
                <a href="/KasiConnect/Pages/add_products.php">Sell</a>
                <a href="/KasiConnect/Pages/my_products.php">My Listings</a>
                <a href="/KasiConnect/Pages/seller_dashboard.php">Seller Dashboard</a>

            <?php else: ?>

                <a href="/KasiConnect/Pages/login.php">Login</a>
                <a href="/KasiConnect/Pages/register.php">Register</a>

            <?php endif; ?>

            <script src="/KasiConnect/JS/apiConfig.js"></script>
        </nav>

    </header>

    <hr>

    <main class="container">