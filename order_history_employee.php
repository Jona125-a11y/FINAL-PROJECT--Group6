<form method="get" action="" class="search-form">
    <input type="text" name="order_no" placeholder="Search by Order No">
    <button type="submit">Search</button>
</form>

<?php 
$title = 'Retrofee | Order History Employee';
include './view/employee_header.php';

// Check if a search term is provided
$order_no_filter = isset($_GET['order_no']) ? trim($_GET['order_no']) : '';

$sql = "SELECT tblorder.*, tblproduct.*, tbluser.*, 
     tblorder.dateandtime AS order_datetime,
     (tblproduct.price * tblorder.qt) AS total_price
     FROM tblorder
     JOIN tblproduct ON tblorder.product_id = tblproduct.product_id
     JOIN tbluser ON tblorder.user_id = tbluser.user_id
     WHERE tblorder.status = 'Complete'";

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
      </div>";
} else {
    echo "<p class='no'>No orders found.</p>";
}
?>
<?php include('./view/employee_footer.php'); ?>
