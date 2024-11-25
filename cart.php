<?php
// Set the title and include the header
$title = 'Retrofee | Cart';
include './view/header.php';

if (!isset($_SESSION['id'])) {
    echo "<p>Error: User not logged in.</p>";
    include './view/footer.php';
    exit;
}
$userId = (int)$_SESSION['id']; // Convert to integer for security

// Query to fetch cart details
$query = "SELECT tblcart.*, tblproduct.*, tbluser.* 
          FROM tblcart 
          JOIN tblproduct ON tblcart.product_id = tblproduct.product_id 
          JOIN tbluser ON tblcart.user_id = tbluser.user_id
          WHERE tblcart.user_id = ?
          ORDER BY tblcart.user_id";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);

if ($stmt->execute()) {
    $results = $stmt->get_result();
} else {
    echo "<p>Error executing query: " . htmlspecialchars($stmt->error) . "</p>";
    include './view/footer.php';
    exit;
}
?>
<div class="cart">
    <div class="cart-container">
        <?php if ($results->num_rows === 0): ?>
            <h1 class="no">No cart found.</h1>
        <?php else: ?>
            <h1 class="sign">Your Cart</h1>
            <?php while ($row = $results->fetch_assoc()): ?>
                <div class="cart-item" data-id="<?php echo (int)$row['cart_id']; ?>" data-price="<?php echo (float)$row['price']; ?>">
                    <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image">
                    <div class="product-details">
                        <p class="product-cart-name"><?php echo htmlspecialchars($row['productname']); ?></p>
                        <p class="product-cart-price">Price: ₱<span><?php echo htmlspecialchars($row['price']); ?></span></p>
                    </div>
                    <div class="quantity-controls">
                        <button class="minus-btn" data-id="<?php echo (int)$row['cart_id']; ?>"><i class="fa fa-minus" aria-hidden="true"></i></button>
                        <span class="quantity"><?php echo (int)$row['qt']; ?></span>
                        <button class="add-btn" data-id="<?php echo (int)$row['cart_id']; ?>"><i class="fa fa-plus" aria-hidden="true"></i></button>
                    </div>
                    <p class="total-price">Total: ₱<?php echo number_format($row['price'] * $row['qt'], 2); ?></p>
                    <button class="delete-btn" data-id="<?php echo (int)$row['cart_id']; ?>"><i class="fa fa-trash" aria-hidden="true"></i></button>
                </div>
            <?php endwhile; ?>
           
            <!-- Payment Method Dropdown -->
            <div class="payment-method-container">
                <select id="payment-method" name="payment-method">
                    <option value="">Payment Method</option>
                    <option value="Cash On Delivery">Cash On Delivery</option>
                    <option value="Gcash">Gcash</option>
                    <option value="Drop-in">Drop-in</option>
                </select>
            </div>
            <button class="order-btn" id="global-order-btn">Order All Items</button>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
   document.getElementById('global-order-btn').addEventListener('click', function() {
    const paymentMethodSelect = document.getElementById('payment-method');
    const paymentMethod = paymentMethodSelect.value;

    if (!paymentMethod) {
        alert('Please select a payment method before ordering.');
        return;
    }

    // Disable the button to prevent multiple submissions
    this.disabled = true;

    const userId = <?php echo $userId; ?>; // Replace this with the actual user ID from your application context
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'process_order.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
    if (xhr.status === 200) {
        if (xhr.responseText.trim() === 'success') {
            window.location.href = 'order.php';  // Redirect to order.php after success
        } else {
            alert('Error processing order');
        }
    } else {
        alert('Error processing order');
    }

    // Re-enable the button if needed
    document.getElementById('global-order-btn').disabled = false;
};

    const params = 'user_id=' + encodeURIComponent(userId) + '&paymentmethod=' + encodeURIComponent(paymentMethod);
    xhr.send(params);
});

    function updateQuantity(cartId, action) {
        $.ajax({
            url: 'update_quantity.php',
            type: 'POST',
            data: { cart_id: cartId, action: action },
            success: function(response) {
                if (response === 'success') {
                    let cartItem = $(`.cart-item[data-id='${cartId}']`);
                    let quantityElement = cartItem.find('.quantity');
                    let quantity = parseInt(quantityElement.text());
                    let price = parseFloat(cartItem.data('price'));

                    quantity = action === 'add' ? quantity + 1 : Math.max(quantity - 1, 1);
                    quantityElement.text(quantity);

                    let totalPrice = price * quantity;
                    cartItem.find('.total-price').text('Total: ₱' + totalPrice.toFixed(2));
                } else {
                    console.error("Error updating quantity:", response);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", error);
            }
        });
    }

    $(document).on('click', '.add-btn', function() {
        let cartId = $(this).data('id');
        updateQuantity(cartId, 'add');
    });

    $(document).on('click', '.minus-btn', function() {
        let cartId = $(this).data('id');
        updateQuantity(cartId, 'minus');
    });

    function deleteCartItem(cartId) {
        $.ajax({
            url: 'delete_from_cart.php',
            type: 'POST',
            data: { cart_id: cartId },
            success: function(response) {
                if (response === 'success') {
                    $(`.cart-item[data-id='${cartId}']`).remove();
                    location.reload();
                } else {
                    console.error("Error deleting item:", response);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", error);
            }
        });
    }

    $(document).on('click', '.delete-btn', function() {
        let cartId = $(this).data('id');
        deleteCartItem(cartId);
    });

    $(document).on('click', '.order-btn', function() {
        let cartItems = [];
        const user_id = <?php echo json_encode($userId); ?>;
        $('.cart-item').each(function() {
            let cartId = $(this).data('id');
            let quantity = parseInt($(this).find('.quantity').text());
            cartItems.push({ cart_id: cartId, quantity: quantity });
        });

        $.ajax({
            url: 'process_order.php',
            type: 'POST',
            data: {
                user_id: user_id,
                items: JSON.stringify(cartItems)
            },
            success: function(response) {
                if (response.trim() === 'success') {
                    alert("Order placed successfully!");
                    window.location.href = 'order.php';
                } else {
                    alert("Error placing order: " + response);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", error);
                alert("AJAX error occurred. Please try again.");
            }
        });
    });
</script>
<?php include('./view/footer.php'); ?>
