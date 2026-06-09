<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION["user_id"] ?? null;

$title = $_POST["title"] ?? "";
$description = $_POST["description"] ?? "";
$price = $_POST["price"] ?? "";

$uploadedImageName = null;
$uploadError = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $imageName = $_FILES["image"]["name"];
    $tmpName = $_FILES["image"]["tmp_name"];

    $targetDir = "../Images/";
    $targetFile = $targetDir . basename($imageName);

    if (move_uploaded_file($tmpName, $targetFile)) {
        $uploadedImageName = $imageName;
    } else {
        $uploadError = "Image upload failed.";
    }
}

?>

<?php
include("../Includes/header.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Products</title>
    <link rel="stylesheet" href="/KasiConnect/CSS/style.css">

</head>

<body>

    <?php if (isset($success)):  ?>
        <p class="success-message"><?php echo $success;  ?></p>
    <?php endif;  ?>

    <?php if (isset($error)):  ?>
        <p class="error-message"></p><?php echo $error  ?></p>
    <?php endif;  ?>

    <div class="listing-page">
        <div class="listing-intro">
            <p class="eyebrow">Seller Centre</p>
            <h2>Create a new listing</h2>
            <p>
                Add a clear photo, honest details, and a fair price so buyers can quickly understand what you are selling.
            </p>

            <div class="listing-tips">
                <div>
                    <strong>clear photo</strong>
                    <span>Use good lighting</span>
                </div>
                <div>
                    <strong>Fair price</strong>
                    <span>Use South African Rand</span>
                </div>
                <div>
                    <strong>Good Details</strong>
                    <span>Mention condition and size</span>
                </div>
            </div>
        </div>

        <div class="form-card listing-form-card">
            <?php if (isset($success)):  ?>
                <p class="success-message"><?php echo $success;  ?></p>
            <?php endif;  ?>

            <?php if (isset($error)):  ?>
                <p class="error-message"><?php echo $error;  ?></p>
            <?php endif;  ?>

            <form id="addProductForm" method="POST" enctype="multipart/form-data">
                <div class="form-group image-upload-box">
                    <label>Product Name</label>
                    <input type="file" id="imageInput" name="image" required>
                    <small>Choose a clear image of the item you are selling</small>
                </div>

                <div class="form-group">
                    <label>Product name</label>
                    <input type="text" id="titleInput" name="title" placeholder="Couch" required>
                </div>

                <div class="form-group">
                    <label>Product description</label>
                    <input type="text" id="descriptionInput" name="description" placeholder="L shapped, brown couch, 9/10 condition" required>
                </div>

                <div class="form-group">
                    <label>Price</label>
                    <div class="price-input">
                        <span>R</span>
                        <input type="number" id="priceInput" name="price" placeholder="5000" required>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Add Product</button>

                <p id="addProductMessage"></p>

            </form>

        </div>

    </div>

    <script>
        const apiBaseUrl = window.KASI_API_BASE_URL;

        const apiToken = <?php echo json_encode($_SESSION["api_token"] ?? null);  ?>;

        const uploadedImageName = <?php echo $uploadedImageName === null ? "null" : json_encode($uploadedImageName); ?>;
        const uploadError = <?php echo $uploadError === null ? "null" : json_encode($uploadError); ?>;

        const submittedTitle = <?php echo json_encode($title); ?>;
        const submittedDescription = <?php echo json_encode($description); ?>;
        const submittedPrice = <?php echo $price === "" ? "null" : json_encode((float)$price); ?>;

        const addProductMessage = document.getElementById("addProductMessage");

        if (uploadError !== null) {
            addProductMessage.textContent = uploadError;
        }

        if (uploadedImageName !== null) {
            createProduct();
        }

        async function createProduct() {
            const productData = {
                title: submittedTitle,
                description: submittedDescription,
                price: submittedPrice,
                image: uploadedImageName
            };

            const response = await fetch(`${apiBaseUrl}/products`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${apiToken}`
                },
                body: JSON.stringify(productData)
            });

            if (!response.ok) {
                const errorText = await response.text();
                addProductMessage.textContent = errorText || "Could not add product.";
                return;
            }

            const createdProduct = await response.json();

            console.log("Created product:", createdProduct);

            addProductMessage.textContent = `Product added successfully. New ID: ${createdProduct.id}`;

            setTimeout(() => {
                window.location.href = "/KasiConnect/Pages/products.php";
            }, 1000);
        }
    </script>


</body>

</html>

<?php
include("../Includes/footer.php");
?>