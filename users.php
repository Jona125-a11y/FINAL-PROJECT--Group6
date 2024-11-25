
<?php
// Set the title and include the header
$title = 'Retrofee | Product';
include './view/admin_header.php';
?>
<div id="product-edit-Modal" class="Editmodal">
  <div class="modal-content">
    <span class="close-edit-btn">&times;</span>
    <div class="form-container">
      <h2>Insert Employee</h2>
     
      <form action="insert_user.php" method="post" enctype="multipart/form-data">
        <label for="product_id">User ID</label>
        <input type="text" id="user_id" name="user_id" required>

        <label for="product_name">Username</label>
        <input type="text" id="username" name="username" required>

        <label for="price">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="category">Contact</label>
        <input type="text" id="contact" name="contact" required>

        <label for="category">Address</label>
        <input type="text" id="address" name="address" required>

        <label for="category">Password</label>
        <input type="password" id="password" name="password" required>

        <button class="btn-insert" type="submit" name="submit">Insert Product</button>
      </form>
    </div>
  </div>
</div>
<button id="openModalBtn"><i class="fas fa-plus"></i></button>
<h1>User List</h1>

<?php
// Database connection check
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM tbluser";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>UserName</th><th>Email</th><th>Contact</th><th>Address</th><th>Action</th></tr>";
    

    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["user_id"] . "</td>";
        echo "<td>" . $row["username"] . "</td>";
        echo "<td>" . $row["email"] . "</td>";
        echo "<td>" . $row["contact"] . "</td>";
        echo "<td>" . $row["address"] . "</td>";
        echo "<td>";
        echo "<a href='delete_user.php?id=" . $row["user_id"] . "' class='btn btn-delete' onclick='return confirm(\"Are you sure you want to delete this product?\")'>Delete</a>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No products found.</p>";
}

$conn->close();
?>

<script>
 // Modal for editing users
const editModal = document.getElementById('product-edit-Modal'); // Correct edit modal ID
const openModalBtn = document.getElementById('openModalBtn'); // Button to open modal
const closeEditModalBtn = document.querySelector('.close-edit-btn'); // Close button for edit modal

// Open edit modal when the button is clicked
openModalBtn.onclick = function() {
  editModal.style.display = 'block';
};

// Close the modal when the 'X' is clicked
closeEditModalBtn.onclick = function() {
  editModal.style.display = 'none';
};

// Close the modal when clicking outside of it
window.onclick = function(event) {
  if (event.target === editModal) {
    editModal.style.display = 'none';
  }
};

</script>

<?php include('./view/admin_footer.php'); ?>
