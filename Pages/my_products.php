<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

?>

<?php include("../Includes/header.php") ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../KasiConnect/CSS/style.css">
    <title>My Listings</title>
</head>

<body>
    <form>
        <h2>My Listings</h2>
        <p id="productsMessage">Loading your Listings...</p>

        <div id="myProductsList"></div>

    </form>

    <script>
        const apiBaseUrl = window.KASI_API_BASE_URL;
        const userId = <?php echo $user_id; ?>;

        const productsMessage = document.getElementById("productsMessage");
        const myProductsList = document.getElementById("myProductsList");

        async function loadMyProducts() {
            const response = await fetch(`${apiBaseUrl}/users/${userId}/products`);

            if (!response.ok) {
                productsMessage.textContent = "Could not load your products.";
                return;
            }

            const products = await response.json();

            if (products.length === 0) {
                productsMessage.textContent = "No products added yet.";
                return;
            }

            productsMessage.textContent = "";
            myProductsList.innerHTML = "";

            products.forEach(product => {
                const productCard = document.createElement("div");
                productCard.className = "product-card";

                productCard.innerHTML = `
                <h3>${product.title ?? ""}</h3>
                <p>${product.description ?? ""}</p>
                <strong>R ${product.price ?? 0}</strong>
                <br><br>

                <button type="button" class="contact-btn" onclick="deleteProduct(${product.id})">
                    Delete
                </button>
            `;

                myProductsList.appendChild(productCard);
            });
        }

        loadMyProducts();

        async function deleteProduct(productId) {
            const confirmed = confirm("are you are you want to delete this product?");

            if (!confirmed) {
                return;
            }

            const response = await fetch(`${apiBaseUrl}/products/${productId}`, {
                method: "DELETE"
            });

            if (!response.ok) {
                productsMessage.textContent = "Could not delete product.";
                return;
            }

            productsMessage.textContent = "Product deleted successfully.";
            loadMyProducts();

        }
    </script>

</body>

</html>

<?php include("../Includes/footer.php") ?>