<?php

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
$currentUserId = $_SESSION["user_id"] ?? null;
?>

<?php include("../Includes/header.php")   ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KasiConnect Product Details</title>
</head>

<body>

    <div id="productDetails"></div>
    <div class="review-section">
        <h3> Customer Reviews</h3>
        <div id="ratingSummary" class="rating-summary"></div>

        <div class="review-form-card">
            <h3>Leave a Review</h3>

            <form id="reviewForm">
                <input type="hidden" name="product_id" value="<?php echo $id; ?>">

                <div class="star-rating">
                    <input type="radio" id="5-stars" name="rating" value="5">
                    <label for="5-stars">★</label>

                    <input type="radio" id="4-stars" name="rating" value="4">
                    <label for="4-stars">★</label>

                    <input type="radio" id="3-stars" name="rating" value="3">
                    <label for="3-stars">★</label>

                    <input type="radio" id="2-stars" name="rating" value="2">
                    <label for="2-stars">★</label>

                    <input type="radio" id="1-stars" name="rating" value="1">
                    <label for="1-stars">★</label>
                </div>

                <label>Review</label>
                <textarea id="reviewText" name="review" placeholder="Share your experience with this product..." required></textarea>
                <button type="submit">Submit Review</button>
                <p id="reviewMessage"></p>
            </form>

        </div>

        <div id="reviewList" class="review-list"></div>
    </div>

    <script>
        const apiBaseUrl = window.KASI_API_BASE_URL;
        const productId = <?php echo $id  ?>;

        const apiToken = <?php echo json_encode($_session["api_token"] ?? null); ?>;

        const currentUserId = <?php echo $currentUserId == null ? "null" : $currentUserId  ?>;
        const productDetails = document.getElementById("productDetails");
        const ratingSummary = document.getElementById("ratingSummary");
        const reviewList = document.getElementById("reviewList");


        async function loadProductDetails() {
            const response = await fetch(`${apiBaseUrl}/products/${productId}`);

            if (!response.ok) {
                productDetails.innerHTML = "<p>Product not found</p>";
                return;
            }

            const product = await response.json();

            productDetails.innerHTML = `
                <div class="details-page">
                    <div class="details-image-panel">
                        <img src="${product.imageUrl}" class="details-image" alt="${product.title ?? "Product image"}">
                    </div>

                    <div class="details-info-panel">
                        <p class="eyebrow">Product Details</p>
                        <h2>${product.title ?? ""}</h2>
                        <p class="details-description">${product.description ?? ""}</p>

                        <div class="details-price">
                            R ${product.price ?? 0}
                        </div>

                        <div class="details-action">
                            <a href="/KasiConnect/Pages/contact_seller.php?product_id=${product.id}" class="contact-btn">
                                Contact Seller
                            </a>
                            <br><br>

                            <button type="button" id="buyButton" class="buy-btn">
                                Buy Now
                            </button>

                            <p id="buyMessage"></p>
                     </div>
                    </div>
             </div>

            `;

            const buyButton = document.getElementById("buyButton");
            const buyMessage = document.getElementById("buyMessage");

            buyButton.addEventListener("click", async () => {
                if (apiToken === null) {
                    buyMessage.textContent = "Please log in beforebuying this product.";
                    return;
                }

                const orderData = {
                    productId: product.id
                };

                const response = await fetch(`${apiBaseUrl}/orders`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": `Bearer ${apiToken}`
                    },
                    body: JSON.stringify(orderData)
                });

                if (!response.ok) {
                    const errorMessage = await response.text();
                    buyMessage.textContent = errorMessage || "Could not place order.";
                    return;
                }

                buyMessage.textContent = "Order placed successfully.";
            })

        }

        async function loadReviews() {
            const response = await fetch(`${apiBaseUrl}/products/${productId}/reviews`);
            if (!response.ok) {
                reviewList.innerHTML = "<p>Could not loadreviews</p>"
            }

            const reviews = await response.json();
            if (reviews.length === 0) {
                ratingSummary.innerHTML = "<p>No ratings yet. Be the first one?</p>";
                reviewList.innerHTML = "<p>No Reviews yet.</p>";
                return;
            }

            const totalRating = reviews.reduce((sum, review) => sum + review.rating, 0);
            const averageRating = (totalRating / reviews.length).toFixed(1);

            ratingSummary.innerHTML = `
                <p>
                    <strong>${averageRating} / 5</strong>
                    from ${reviews.length} reviews
                </p>
            `;

            reviewList.innerHTML = "";

            reviews.forEach(review => {
                const stars = "★".repeat(review.rating);

                reviewList.innerHTML += `
                    <div class="review-card">
                        <h4>${review.userName ?? `User ${review.userId}`}</h4>
                        <p>${stars}</p>
                        <p>${review.reviewText}</p>
                    </div>
                `;
            });

        }

        const reviewForm = document.getElementById("reviewForm");
        const reviewText = document.getElementById("reviewText");
        const reviewMessage = document.getElementById("reviewMessage");

        reviewForm.addEventListener("submit", async event => {
            event.preventDefault();

            if (apiToken === null) {
                reviewMessage.textContent = "Please log in before submitting a review";
                return;
            }

            const selectedRating = document.querySelector("input[name='rating']:checked");
            if (selectedRating === null) {
                reviewMessage.textContent = "Please select a rating";
                return;
            }

            const reviewData = {
                rating: Number(selectedRating.value),
                reviewText: reviewText.value
            };

            const response = await fetch(`${apiBaseUrl}/products/${productId}/reviews`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${apiToken}`
                },
                body: JSON.stringify(reviewData)
            });

            if (!response.ok) {
                reviewMessage.textContent = "Could not submit review.";
                return;
            }

            reviewMessage.textContent = "Review Submitted Successfully.";
            reviewForm.reset();



        })

        loadProductDetails();
        loadReviews();
    </script>


</body>

</html>

<?php include("../Includes/footer.php")   ?>