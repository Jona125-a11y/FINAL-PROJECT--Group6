<?php
// Set the title and include the header
$title = 'Retrofee | Order';
include './view/header.php';

$user_id = $_SESSION['id'];
if (!isset($_SESSION['id'])) {
    echo "<p>You must be logged in to view this page.</p>";
    exit;
}


// Fetch logged-in user data from the database
$userId = $_SESSION['id'];
$query = "SELECT username, email FROM tbluser WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();

// Updated query without the admin_id, since it's a single store
$query = "SELECT tblorder.*, tblproduct.*
          FROM tblorder 
          JOIN tblproduct ON tblorder.product_id = tblproduct.product_id 
          WHERE tblorder.user_id = ? AND tblorder.status IN ('Pending', 'Processing','Ongoing', 'Delivered')
          ORDER BY tblorder.dateandtime DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$results = $stmt->get_result();

$current_date = null;
$grand_total = 0;
?>

<?php if ($results->num_rows === 0): ?>
        <h1 class="no orderFound">No orders found.</h1>
    <?php else: ?>
        <div class="orders-container">
        <h1 class="sign">Your Orders</h1>
            <?php
            $order_subtotal = 0;
            while ($row = $results->fetch_assoc()): 
                if ($current_date !== $row['dateandtime']) {
                    if ($current_date !== null) {
                        echo "</tbody></table><tr><td colspan='2' class='subtotal-label'>Subtotal:</td>
                              <td class='subtotal-amount'>₱" . number_format($order_subtotal, 0) . "</td></tr>";
                        echo '</div>'; 
                        $grand_total += $order_subtotal;
                        $order_subtotal = 0; 
                    }

                    $current_date = $row['dateandtime'];
                    echo '<div class="order-group">';
                    echo "<p class='order-status payment'>" . htmlspecialchars($row['payment']) . "</p>";
                    echo "<p class='order-status order'>Order No: " . htmlspecialchars($row['order_no']) . "</p>";
                    echo "<p class='order-status'>Status: " . htmlspecialchars($row['status']) . "</p>";
                    echo "<p class='order-status'>Payment Method: " . htmlspecialchars($row['pay_method']) . "</p>";
                    echo "<p class='order-date'>Order Date: " . date('Y-m-d H:i:s', strtotime($current_date)) . "</p>";
                    echo '<table class="order-table">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Total Price</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>';
                }

                $item_total = $row['price'] * $row['qt'];
                $order_subtotal += $item_total;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['productname']); ?></td>
                    <td>₱<?php echo number_format($item_total, 0); ?></td>
                    <td><?php echo $row['qt']; ?></td>
                </tr>
               
            <?php endwhile; ?>

            <?php if ($current_date !== null): ?>
                </tbody>
                </table>
                    <td colspan="2" class="subtotal-label">Subtotal:</td>
                    <td class="subtotal-amount">₱<?php echo number_format($order_subtotal, 0); ?></td>
                   
                </div>
                <?php $grand_total += $order_subtotal; ?>
            <?php endif; ?>
            
            <div class="grand-total">
                <h3 class="h3-grantotal">Grand Total: ₱<?php echo number_format($grand_total, 0); ?></h3>
            </div>
        </div>
    <?php endif; ?>


<?php include('./view/footer.php'); ?>