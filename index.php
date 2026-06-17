<?php

$sql = "SELECT * FROM products ORDER by created_at DESC LIMIT 4";
$result = $conn->query($sql);

$featuredProducts = [];

while ($row = $result->fetch_assoc()) {
    $featuredProducts[] = $row;
}

?>

<?php
include("../KasiConnect/Includes/header.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KasiConnect</title>
    <link rel="stylesheet" href="../KasiConnect/CSS/style.css">
</head>


<body>

    <div class="hero">
        <div class="hero-content">
            <p class="eyebrow">Township Marketplace</p>
            <h1>KasiConnect</h1>
            <p>A trusted space where township buyers and sellers connect, trade, and support local businesses</p>

            <?php if (isset($_SESSION["user"])):  ?>
                <a href="/KasiConnect/Pages/products.php" class="hero-btn">Explore Marketplace</a>
            <?php else:  ?>
                <div class="hero-actions">
                    <a href="/KasiConnect/Pages/register.php" class="hero-btn">Create Account</a>
                    <a href="/KasiConnect/Pages/login.php" class="hero-btn secondary-btn">Login</a>
                </div>
            <?php endif;  ?>
        </div>
    </div>

    <div class="container">
        <h2> Featured products</h2>
        <div class="product-grid">
            <?php foreach ($featuredProducts as $product):  ?>
                <div class="product-card">
                    <img src="../KasiConnect/Images/<?php echo htmlspecialchars($product['image']) ?>" class="product-image">
                    <h3><?php echo htmlspecialchars($product["title"])  ?></h3>
                    <p><?php echo htmlspecialchars($product["description"])  ?></p>
                    <strong>R <?php echo htmlspecialchars($product["price"])  ?></strong>
                    <br><br>

                    <a href="/KasiConnect/Pages/product_details.php?id=<?php echo $product["id"]  ?>"
                        class="buy-btn">View Product</a>
                </div>
            <?php endforeach;  ?>
        </div>
    </div>

    <div class="info-section">
        <h2>Why KasiConnect?</h2>
        <div class="info-grid">
            <div class="info-card">
                <h3>Built on Trust</h3>
                <p>Buy and sell within a familiar community where local connections matter.</p>
            </div>
            <div class="info-card">
                <h3>Simple and local</h3>
                <p>Designed for everyday local township trading.</p>
            </div>
            <div class="info-card">
                <h3>Empower Local Sellers</h3>
                <p>Helping informal traders grow and reach more customers nearby.</p>
            </div>
        </div>
    </div>

</body>

</html>

<?php
include("../KasiConnect/Includes/footer.php");
?>