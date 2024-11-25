<?php
// Set the title and include the header
$title = 'Retrofee | Menu';
include './view/header.php';
// Determine search term and filter
$searchTerm = isset($_GET['order_no']) ? "%" . $conn->real_escape_string($_GET['order_no']) . "%" : '%';
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';

// SQL query to fetch trending products (based on top 4 bestsellers in tblorder)
$trendingSql = "SELECT p.product_id, p.image, p.productname, p.price, SUM(o.qt) AS total_sales 
                FROM tblorder o
                JOIN tblproduct p ON o.product_id = p.product_id
                GROUP BY o.product_id
                ORDER BY total_sales DESC
                LIMIT 4";

// SQL query to fetch products by category or search term
$sql = "SELECT product_id, image, productname, price 
        FROM tblproduct 
        WHERE productname LIKE ?";

// Add category condition if specified
if ($category) {
    $sql .= " AND category = ?";
}

// Prepare statements
$stmt = $conn->prepare($sql);
if ($category) {
    $stmt->bind_param("ss", $searchTerm, $category);
} else {
    $stmt->bind_param("s", $searchTerm);
}
$stmt->execute();
$result = $stmt->get_result();

$trendingStmt = $conn->prepare($trendingSql);
$trendingStmt->execute();
$trendingResult = $trendingStmt->get_result();
?>

<!-- Search Form -->
<form method="get" action="" class="search-form">
    <input type="text" name="order_no" placeholder="Search..." value="<?php echo isset($_GET['order_no']) ? htmlspecialchars($_GET['order_no']) : ''; ?>">
    <button type="submit"><i class="fas fa-search"></i></button>
</form>

<!-- Category Buttons -->
<form action="" method="get" class="cat">
    <button type="submit" name="category" value="">All</button>
    <button type="submit" name="category" value="Trending">&#128293; Trending</button>
    <button type="submit" name="category" value="Hot Coffee"> &#x2615;Hot Coffee</button>
    <button type="submit" name="category" value="Cold Coffee">&#x1F9CA; Cold Coffee</button>
    <button type="submit" name="category" value="Pastries">&#x1F370; Pastries</button>
</form>
<div id="cartMessage" style="display:none;">Added to your cart!</div>
<div class="menu">
<?php if (isset($_GET['category']) && $_GET['category'] === 'Trending' && $trendingResult->num_rows > 0): ?>
    <div class="product-container-trend">
        <?php while($row = $trendingResult->fetch_assoc()): ?>
            <div class="product-card">
                <img src="<?php echo htmlspecialchars($row["image"]); ?>" alt="<?php echo htmlspecialchars($row["productname"]); ?>">
                <div class="product-name"><?php echo htmlspecialchars($row["productname"]); ?></div>
                <div class="product-price">₱<?php echo number_format($row["price"], 2); ?></div>
                <button class="add-to-cart" onclick="addToCart(<?php echo $row['product_id']; ?>)"><i class="fas fa-plus"></i> Add to Cart</button>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>
    <!-- Display Category/Search Products -->
    <div class="product-container">
<?php if ($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
        <div class="product-card">
            <img src="<?php echo htmlspecialchars($row["image"]); ?>" alt="<?php echo htmlspecialchars($row["productname"]); ?>">
            <div class="product-name"><?php echo htmlspecialchars($row["productname"]); ?></div>
            <div class="product-price">₱<?php echo number_format($row["price"], 2); ?></div>
            <button class="add-to-cart" onclick="addToCart(<?php echo $row['product_id']; ?>)"><i class="fas fa-plus"></i> Add to Cart</button>
        </div>
    <?php endwhile; ?>
<?php elseif (!(isset($_GET['category']) && $_GET['category'] === 'Trending')): ?>
    <!-- Display this message only if not viewing Trending -->
    <p class="no">No products found.</p>
<?php endif; ?>
</div>

<script>
function addToCart(productId) {
    // Get the user ID, you may need to fetch it dynamically depending on the session
    var userId = <?php echo $_SESSION['id']; ?>;  

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "add_to_cart.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Display success message in a paragraph element
            var cartMessage = document.getElementById("cartMessage");
            cartMessage.style.display = "block";

            // Hide the message after 3 seconds
            setTimeout(function() {
                cartMessage.style.display = "none";
            }, 3000);
        }
    };
    xhr.send("product_id=" + productId + "&user_id=" + userId);
}
</script>

<?php
// Include footer
include('./view/footer.php');
?>
