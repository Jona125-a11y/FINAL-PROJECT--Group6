<form method="get" action="" class="search-form">
    <input type="text" name="order_no" placeholder="Search by Order No">
    <button type="submit">Search</button>
</form>

<?php 
$title = 'Retrofee | Employee';
include './view/employee_header.php';

// Check if a search term is provided
$order_no_filter = isset($_GET['order_no']) ? trim($_GET['order_no']) : '';

// Prepare the base SQL query
$sql = "SELECT tblorder.*, tblproduct.*, tbluser.*, 
     tblorder.dateandtime AS order_datetime,
     (tblproduct.price * tblorder.qt) AS total_price
     FROM tblorder
     JOIN tblproduct ON tblorder.product_id = tblproduct.product_id
     JOIN tbluser ON tblorder.user_id = tbluser.user_id
     WHERE tblorder.status = 'Pending'";

// Optional filter for order number
if ($order_no_filter !== '') {
 $sql .= " AND tblorder.order_no = ?";
}

$sql .= " ORDER BY order_datetime ASC";

$stmt = $conn->prepare($sql);
if ($order_no_filter !== '') {
 $stmt->bind_param("s", $order_no_filter);
}
$stmt->execute();
$result = $stmt->get_result();

// Check if there are pending orders
$last_order_date = null;
$order_subtotal = 0; // Initialize the subtotal

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // If a new order date is encountered, close the previous container and start a new one
        if ($last_order_date !== $row['dateandtime']) {
            // Close previous container if it exists
            if ($last_order_date !== null) {
               
                echo "</tbody></table>
                <p class='subtotal'>Subtotal: ₱" . number_format($order_subtotal, 0) . "</p>
                    <form>
                        <input type='hidden' name='order_date' value='{$last_order_date}'>
                        <button type='button' class='btn processing'>Processing</button>
                    </form>
                 </div>";
                // Reset subtotal for the new order date
                $order_subtotal = 0;
            }

            // Start a new container and table for the new date
            $last_order_date = $row['dateandtime'];
            echo "<div class='container'>";
            echo "<h2 class='info orderNo'>Order No: {$row['order_no']}</h2>";
            echo "<h2 class='info'>Customer Name: {$row['username']}</h2>";
            echo "<h2 class='info'>Order Date: {$last_order_date}</h2>";
            echo "<h2 class='info'>Payment Method: {$row['pay_method']}</h2>";
            echo "<h2 class='info'>Status: {$row['status']}</h2>";
            echo "
            <h2 class='info payment'>
                <span class='dropdown' onmouseover='toggleDropdown(this)' onmouseout='toggleDropdown(this)'>
                    {$row['payment']}
                    <div class='dropdown-content'>
                    <a href='#' onclick='updatePaymentStatus(\"{$row['order_no']}\", \"Not Paid\")'>Not Paid</a>
                        <a href='#' onclick='updatePaymentStatus(\"{$row['order_no']}\", \"Paid\")'>Paid</a>
                        <a href='#' onclick='updatePaymentStatus(\"{$row['order_no']}\", \"Cancel\")'>Cancel</a>
                    </div>
                </span>
              </h2>            
            ";
            echo "<table>
                    <thead>
                        <tr>
                            <th>Order Name</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                    <tbody>";
        }

        $item_total = $row['price'] * $row['qt'];
        $order_subtotal += $item_total;

        // Display each row within the table
        echo "<tr>
                <td>{$row['productname']}</td>
                <td>{$row['qt']}</td>
                <td>₱" . number_format($row['total_price'], 0) . "</td>
              </tr>";
    }

    echo "</tbody></table>
    <p class='subtotal'>Subtotal: ₱" . number_format($order_subtotal, 0) . "</p>
        <form>
    <input type='hidden' name='order_date' value='{$last_order_date}'>
    <button type='button' class='btn processing'>Processing</button>
</form>
      </div>";
} else {
    echo "<p class='no'>No orders found.</p>";
}
?>

<script>
window.onload = function() {
    // Attach an event listener to all buttons with the 'processing' class
    const processingButtons = document.querySelectorAll('.btn.processing');
    processingButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default form submission
            
            // Retrieve the order date and status
            const orderDate = this.previousElementSibling.value; // Hidden input value
            const status = this.textContent.trim(); // Button text as status
            
            // Create and configure an XMLHttpRequest object
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "update_status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            
            // Prepare data to be sent
            const data = "status=" + encodeURIComponent(status) + "&order_date=" + encodeURIComponent(orderDate);
            
            // Handle the response
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        console.log(xhr.responseText); // Check response for debugging
                        // Reload the page after updating status
                        location.reload();
                    } else {
                        console.error("Error with the request: " + xhr.status); // Debugging output for errors
                    }
                }
            };
            
            // Send the AJAX request
            xhr.send(data);
        });
    });
};
</script>
<?php include('./view/employee_footer.php'); ?>
