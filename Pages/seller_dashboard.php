<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$seller_id = $_SESSION["user_id"];

include("../Includes/header.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard</title>
</head>



<body>
    <h2>Sellers Dashboard</h2>

    <p id="sellerOrdersMessage"></p>

    <p id="sellerOrdersList"></p>

    <script>
        const apiBaseUrl = window.KASI_API_BASE_URL;
        const sellerId = <?php echo $seller_id;  ?>;

        const sellerOrdersMessage = document.getElementById("sellerOrdersMessage");
        const sellerOrdersList = document.getElementById("sellerOrdersList");

        const apiToken = <?php echo json_encode($_SESSION["api_token"] ?? null);  ?>;

        async function loadSellerOrders() {
            const response = await fetch(`${apiBaseUrl}/sellers/${sellerId}/orders`);

            if (!response.ok) {
                sellerOrdersMessage.textContent = "Could not load seller orders.";
                return;
            }

            const orders = await response.json();

            if (orders.length === 0) {
                sellerOrdersMessage.textContent = "No orders yet.";
                return;
            }

            sellerOrdersMessage.textContent = "";
            sellerOrdersList.innerHTML = "";

            orders.forEach(order => {
                const orderCard = document.createElement("div");
                orderCard.className = "product-card";

                orderCard.innerHTML =
                    `
                    <h3>${order.productTitle ?? "Product"}</h3>
                    <p><strong>
                        Buyer:</strong> ${order.buyerName ?? `User${order.buyerId}`}
                    </p>
                    <p><strong>
                        Status:</strong> ${order.status ?? "Pending"}
                    </p>

                    <button type="button" class="buy-btn" onclick="updateOrderStatus(${order.id}, 'Completed')">
                    Mark Completed
                    </button>
                    <button type="button" class="contact-btn" onclick="updateOrderStatus(${order.id}, 'Cancelled')">
                    Cancel Order
                    </button>
                `;

                sellerOrdersList.appendChild(orderCard);
            });
        }

        async function updateOrderStatus(orderId, status) {
            const response = await fetch(`${apiBaseUrl}/orders/${orderId}/status`, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${apiToken}`
                },
                body: JSON.stringify({
                    status: status
                })
            });

            if (!response.ok) {
                sellerOrdersMessage.textContent = "Could not update order status.";
                return;
            }

            sellerOrdersMessage.textContent = `Order marked as ${status}.`;
            loadSellerOrders();
        }

        loadSellerOrders();
    </script>



</body>

</html>

<?php include("../Includes/footer.php")   ?>