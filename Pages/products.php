<?php include("../Includes/header.php"); ?>


<div class="marketplace-page">
    <div class="marketplace-header">
        <div>
            <p class="eyebrow">Marketplace</p>
            <h2> Browse Local Listings</h2>
            <p>Find products being sold by people in your community</p>
        </div>
    </div>

    <form id="searchForm" class="search-form">
        <input type="text" id="searchInput" name="search" placeholder="Search products..." value="<?php echo $_GET['search'] ?? '' ?>">
        <button type="submit">Search</button>
    </form>

    <div id="productsGrid" class="product-grid marketplace-grid"></div>

    <div id="emptyState" class="empty-state" style="display: none;" ;>
        <p>No products available</p>
    </div>

</div>

<script>
    const apiBaseUrl = window.KASI_API_BASE_URL;

    const searchForm = document.getElementById("searchForm");
    const searchInput = document.getElementById("searchInput");
    const productsGrid = document.getElementById("productsGrid");
    const emptyState = document.getElementById("emptyState");

    async function loadProducts(search = "") {
        const url = search ?
            `${apiBaseUrl}/products?search=${encodeURIComponent(search)}` :
            `${apiBaseUrl}/products`;

        const response = await fetch(url);
        const products = await response.json();

        renderProducts(products);
    }

    function renderProducts(products) {
        productsGrid.innerHTML = "";

        if (products.length === 0) {
            emptyState.style.display = "block";
            return;
        }

        emptyState.style.display = "none";

        products.forEach(product => {
            const productLink = document.createElement("a");
            productLink.href = `/KasiConnect/Pages/product_details.php?id=${product.id}`;
            productLink.className = "product-link";

            productLink.innerHTML = `
                    <div class="product-card marketplace-card">
                        <img src="${product.imageUrl}" class="product-image" alt="${product.title ?? "Product image"}">

                        <div class="product-card-body">
                            <h3>${product.title ?? ""}</h3>
                            <p>${product.description ?? ""}</p>

                            <div class="product-card-footer">
                                <strong>R ${product.price ?? 0}</strong>
                                <span>View</span>
                            </div>
                        </div>
                    </div>
                `;

            productsGrid.appendChild(productLink);
        });
    }

    searchForm.addEventListener("submit", event => {
        event.preventDefault();
        loadProducts(searchInput.value);
    });

    loadProducts();
</script>



<?php include("../Includes/footer.php"); ?>