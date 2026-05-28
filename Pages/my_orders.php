<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION["user_id"] ?? null;

include("../Includes/header.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
</head>

<body>
    <h2>My Orders</h2>

    <p id="ordersMessage">Loading orders...</p>
    <div id="ordersList"></div>

    <script>
        const apiBaseUrl = window.KASI_API_BASE_URL;
        const userId = <?php echo $user_id === null ? "null" : $user_id; ?>;

        const ordersMessage = document.getElementById("ordersMessage");
        const ordersList = document.getElementById("ordersList");

        async function loadOrders() {
            if (userId === null) {
                ordersMessage.textContent = "Please login to view your orders.";
                return;
            }

            const url = `${apiBaseUrl}/users/${userId}/orders`;
            ordersMessage.textContent = `Loading orders for user ${userId}...`;

            const response = await fetch(url);

            if (!response.ok) {
                const errorText = await response.text();
                ordersMessage.textContent = `Could not load your orders. Status: ${response.status}. ${errorText}`;
                return;
            }

            const orders = await response.json();

            if (orders.length === 0) {
                ordersMessage.textContent = "You have no orders yet.";
                return;
            }

            ordersMessage.textContent = "";
            ordersList.innerHTML = "";

            orders.forEach(order => {
                const orderCard = document.createElement("div");
                orderCard.className = "product-card";

                orderCard.innerHTML = `
                    <h3>${order.productTitle ?? "Product unavailable"}</h3>
                    <p>Status: ${order.status ?? "Pending"}</p>
                    <p>Order ID: ${order.id}</p>
                `;

                ordersList.appendChild(orderCard);
            });
        }

        loadOrders();
    </script>
</body>

</html>

<?php include("../Includes/footer.php") ?>