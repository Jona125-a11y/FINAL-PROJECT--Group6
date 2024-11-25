
<?php
// Set the title and include the header
$title = 'Retrofee | Product';
include './view/admin_header.php';
?>

<div id="productModal" class="modal">
  <div class="modal-content">
    <span class="close-btn">&times;</span>
    <div class="form-container">
      <h2>Edit Product</h2>
      <img id="current_image" src="" alt="Current Product Image" class="current-image">
      <form action="edit_product.php" method="post" enctype="multipart/form-data">
        <input type="hidden" id="product_id" name="product_id" required>

        <label for="product_name">Product Name</label>
        <input type="text" id="product_name" name="product_name" required>

        <label for="price">Price</label>
        <input type="number" id="price" name="price" step="0.01" required>

        <label for="category">Category</label>
        <input type="text" id="category" name="category" required>

        <label for="image">Product Image</label>
        <input type="file" id="image" name="image" accept="uploads/*">

        <button class="btn-insert" type="submit" name="submit">Update Product</button>
      </form>
    </div>
  </div>
</div>
<div id="product-edit-Modal" class="Editmodal">
  <div class="modal-content">
    <span class="close-edit-btn">&times;</span>
    <div class="form-container">
      <h2>Insert Product</h2>
     
      <form action="insert_product.php" method="post" enctype="multipart/form-data">
        <label for="product_id">Product ID</label>
        <input type="text" id="product_id" name="product_id" required>

        <label for="product_name">Product Name</label>
        <input type="text" id="product_name" name="product_name" required>

        <label for="price">Price</label>
        <input type="number" id="price" name="price" step="0.01" required>

        <label for="category">Category</label>
        <input type="text" id="cate" name="category" required>

        <label for="image">Product Image</label>
        <input type="file" id="image" name="image" accept="image/*" required>

        <button class="btn-insert" type="submit" name="submit">Insert Product</button>
      </form>
    </div>
  </div>
</div>
<button id="openModalBtn"><i class="fas fa-plus"></i></button>
<h1>Product List</h1>

<?php
// Database connection check
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT product_id, productname, price, category, image FROM tblproduct";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Product Name</th><th>Price</th><th>Category</th><th>Image</th><th>Actions</th></tr>";
    

    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["product_id"] . "</td>";
        echo "<td>" . $row["productname"] . "</td>";
        echo "<td>₱" . $row["price"] . "</td>";
        echo "<td>" . $row["category"] . "</td>";
        echo "<td><img src='" . $row["image"] . "' alt='Product Image' class='product-image' ></td>";
        echo "<td>";
        echo "<button class='btn' data-id='" . $row["product_id"] . "' data-name='" . $row["productname"] . "' data-price='" . $row["price"] . "' data-category='" . $row["category"] . "' data-image='" . $row["image"] . "'>Edit</button>";
        echo "<a href='delete_product.php?id=" . $row["product_id"] . "' class='btn btn-delete' onclick='return confirm(\"Are you sure you want to delete this product?\")'>Delete</a>";
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
  const modal = document.getElementById('productModal');
  const closeModalBtn = document.querySelector('.close-btn');
  const editButtons = document.querySelectorAll('.btn');

  const editModal = document.getElementById('product-edit-Modal');
  const openModalBtn = document.getElementById('openModalBtn');
  const closeEditModalBtn = document.querySelector('.close-edit-btn');

  openModalBtn.onclick = function() {
    editModal.style.display = 'block'; // Corrected variable name
  };

  closeModalBtn.onclick = function() {
    modal.style.display = 'none';
  };

  closeEditModalBtn.onclick = function() {
    editModal.style.display = 'none'; // Corrected modal close handling
  };

  window.onclick = function(event) {
    if (event.target === modal) {
      modal.style.display = 'none';
    }
    if (event.target === editModal) { // Added handling for edit modal
      editModal.style.display = 'none';
    }
  };

  editButtons.forEach(button => {
  button.onclick = function() {
    const productId = button.dataset.id;
    const productName = button.dataset.name;
    const productPrice = button.dataset.price;
    const productCategory = button.dataset.category;
    const productImage = button.dataset.image;

    // Fill the form fields with the product data
    document.getElementById('product_id').value = productId;
    document.getElementById('product_name').value = productName;
    document.getElementById('price').value = productPrice;
    document.getElementById('category').value = productCategory;

    // Set the image src and make it visible
    const currentImage = document.getElementById('current_image');
    if (productImage) {
      currentImage.src = productImage;
      currentImage.style.display = 'block'; // Show the image
    } else {
      currentImage.style.display = 'none'; // Hide if no image
    }

    // Show the modal
    modal.style.display = 'block';
  };
});
</script>

<?php include('./view/admin_footer.php'); ?>
