<?php
session_start();
require 'db.php';

$limit = 15; // Number of entries to show per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Get the current page or set to 1
$offset = ($page - 1) * $limit; // Calculate the offset for the query

// Fetch all products for searching
$all_products_query = "SELECT * FROM inventory";
$all_products_result = mysqli_query($conn, $all_products_query);
$all_products = mysqli_fetch_all($all_products_result, MYSQLI_ASSOC);

// Get products for the current page
$query = "SELECT * FROM inventory LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);


// Handle delete product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $product_id = $_POST['I_ID'];
    $delete_query = "DELETE FROM inventory WHERE I_ID = ?";

    if ($stmt = $conn->prepare($delete_query)) {
        $stmt->bind_param("i", $product_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Product deleted successfully.";
        } else {
            $_SESSION['error'] = "Error deleting product: " . $conn->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Error preparing delete query: " . $conn->error;
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle edit product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
    $I_ID = $_POST['I_ID'];
    $I_Product = $_POST['I_Product'];
    $I_Quantity = $_POST['I_Quantity'];
    $I_Location = $_POST['I_Location'];
    $I_Unit = $_POST['I_Unit'];
    $I_SN = $_POST['I_SN'];

    $edit_query = "UPDATE inventory SET I_Product = ?, I_Quantity = ?, I_SN = ?, I_Location = ?, I_Unit = ? WHERE I_ID = ?";

    if ($stmt = $conn->prepare($edit_query)) {
        $stmt->bind_param("sisssi", $I_Product, $I_Quantity, $I_SN, $I_Location, $I_Unit, $I_ID);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Product updated successfully.";
        } else {
            $_SESSION['error'] = "Error updating product: " . $conn->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Error preparing edit query: " . $conn->error;
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle add product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    function sanitize_input($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    $I_Product = sanitize_input($_POST['I_Product']);
    $I_Quantity = sanitize_input($_POST['I_Quantity']);
    $I_Unit = sanitize_input($_POST['I_Unit']);
    $I_Location = sanitize_input($_POST['I_Location']);
    $I_SN = !empty($_POST['I_SN']) ? sanitize_input($_POST['I_SN']) : NULL;

    $sql = "INSERT INTO inventory (I_Product, I_Quantity, I_SN, I_Location, I_Unit) VALUES (?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sisss", $I_Product, $I_Quantity, $I_SN, $I_Location, $I_Unit);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Product added successfully.";
        } else {
            $_SESSION['error'] = "Error adding product: " . $conn->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Error preparing insert query: " . $conn->error;
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Get total number of products for pagination
$total_query = "SELECT COUNT(*) FROM inventory";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_row($total_result);
$total_products = $total_row[0];
$total_pages = ceil($total_products / $limit);

// Get products for the current page
$query = "SELECT * FROM inventory LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="CSS/CSS_Inventory.css">
    <link href='https://fonts.googleapis.com/css?family=Roboto Slab' rel='stylesheet'>
    <style>
        body {
            background: url('pictures/Inventory_Bg.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        
    </style>
</head>
<body>
<div class="logout tooltip-container">
    <button type="button" class="btn btn-danger btn-lg" onclick="document.location='Index.php'" style="width: 50px; height: 50px; border-radius: 50%; padding: 0;">
        <img src="pictures/logout.svg" alt="Logout" style="padding-left: 3px; padding-bottom: 3px; width: 25px; height: 30px; filter: invert(100%);">
    </button>
    <div class="tooltip">Logout</div>
</div>

    
<form class="signup-container">
    <h2 class="text-center" style="font-family: 'Roboto slab', sans-serif;  font-size: 50px">House Keeping Inventory</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']); 
            ?>
        </div>
    <?php endif; ?>

    <div class="button-container">
    <button type="button" class="btn btn-success btn-lg" onclick="openAddProductModal()">Add Product</button>

    <div class="search-bar">
            <input id="search-input" type="search" class="form-control" placeholder="Search for a product"/>
            <button id="search-button" type="button" class="btn btn-primary">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>

    <div class="table-container">
        <table class="table table-bordered">
        <thead>
                <tr>
                    <th scope="col" class="active">Item Description</th>
                    <th scope="col" class="active">Serial No.</th>
                    <th scope="col" class="active">Quantity</th>
                    <th scope="col" class="active">Location</th>
                    <th scope="col" class="active">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) {  ?>          
                <tr>
                    <td> <?php echo $row['I_Product']; ?> </td>
                    <td> <?php echo ($row['I_SN'] == 0) ? 'N/A' : $row['I_SN']; ?> </td>
                    <td> <?php echo $row['I_Quantity'] . ' ' . $row['I_Unit']; ?> </td>
                    <td> <?php echo $row['I_Location']; ?> </td>
                    <td>
                    <button type="button" class="btn btn-primary" onclick="openEditModal(<?php echo $row['I_ID']; ?>, '<?php echo $row['I_Product']; ?>', '<?php echo $row['I_Quantity']; ?>', '<?php echo $row['I_Location']; ?>', '<?php echo $row['I_Unit']; ?>', '<?php echo $row['I_SN']; ?>')">Edit</button>              
                    <button type="button" class="btn btn-danger" onclick="openModal(<?php echo $row['I_ID']; ?>)">Delete</button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>  
    </div>

    <div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>" class="btn btn-primary">Previous</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>" class="btn <?php echo ($i === $page) ? 'btn-success' : 'btn-secondary'; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?>" class="btn btn-primary">Next</a>
    <?php endif; ?>
</div>

</form>

<!-- Delete Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <p>Are you sure you want to delete this product?</p>
        <form method="post" action="">
            <input type="hidden" id="deleteProductId" name="I_ID" value="">
            <button type="submit" name="delete" class="btn btn-danger">Yes</button>
            <button type="button" class="btn btn-light" onclick="closeModal()">No</button>
        </form>
    </div>
</div>

<!-- Add Product Modal -->
<div id="addProductModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeAddProductModal()">&times;</span>
        <h3>Add New Product</h3>
        <form method="POST">
            <input type="hidden" name="add_product" value="1">

            <!-- Product Name Input -->
            <div class="form-group">
                <label for="I_Product">Product Name</label>
                <input type="text" id="I_Product" name="I_Product" class="form-control" required>
            </div>

            <!-- Quantity Input -->
            <div class="form-row" style="display: flex; gap: 10px;">
                <div class="form-group" style="flex: 1;">
                    <label for="I_Quantity">Quantity</label>
                    <input type="number" id="I_Quantity" name="I_Quantity" class="form-control" required>
                </div>

            <!-- Unit Input -->
            <div class="form-group" style="flex: 1;">
                <label for="I_Unit">Unit</label>
                    <select id="I_Unit" name="I_Unit" class="form-control" required>
                        <option value="" disabled selected>Select Unit</option>
                        <option value="pieces">Pieces</option>
                        <option value="kg">Kg</option>
                        <option value="litre">Litre</option>
                        <option value="meter">Meter</option>
                        <option value="feet">Feet</option>
                        <option value="inches">Inches</option>
                    </select>
                </div>
            </div>

            
            <!-- Serial Number Input -->
            <div class="form-group">
                <label for="I_SN">Serial Number</label>
                <input type="text" id="I_SN" name="I_SN" class="form-control">
            </div>

            <!-- Location Input -->
            <div class="form-group">
                <label for="I_Location">Location</label>
                <input type="text" id="I_Location" name="I_Location" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">Add Product</button>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h3>Edit Product</h3>
        <form method="post" action="">
            <input type="hidden" id="editProductId" name="I_ID" value="">

            <div class="form-group">
                <label for="editProductName">Product Name:</label>
                <input type="text" class="form-control" id="editProductName" name="I_Product" value="" readonly>
            </div>

            <div class="form-row" style="display: flex; gap: 10px;">
                <div class="form-group" style="flex: 1;">
                    <label for="editQuantity">Quantity:</label>
                    <input type="number" class="form-control" id="editQuantity" name="I_Quantity" value="" required>
                </div>

                <div class="form-group" style="flex: 1;">
                    <label for="editUnit">Unit:</label>
                    <select class="form-control" id="editUnit" name="I_Unit" required>
                        <option value="" disabled>Select Unit</option>
                        <option value="pieces">Pieces</option>
                        <option value="kg">Kg</option>
                        <option value="litre">Litre</option>
                        <option value="meter">Meter</option>
                        <option value="feet">Feet</option>
                        <option value="inches">Inches</option>
                    </select>
                </div>
            </div>


            <div class="form-group">
                <label for="editSerialNumber">Serial Number:</label>
                <input type="text" class="form-control" id="editSerialNumber" name="I_SN" value="" required>
            </div>     

            <div class="form-group">
                <label for="editLocation">Located at:</label>
                <input type="text" class="form-control" id="editLocation" name="I_Location" value="" required>
            </div>

            <button type="submit" name="edit" class="btn btn-success">Save Changes</button>
        </form>
    </div>
</div>

</form>
<script>
    function openModal(productId) {
        document.getElementById('deleteModal').style.display = 'block';
        document.getElementById('deleteProductId').value = productId;
    }

    function closeModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    function openEditModal(productId, productName, productQuantity, productLocation, productQuantityUnit, productSN) {
    document.getElementById('editModal').style.display = 'block';
    document.getElementById('editProductId').value = productId;
    document.getElementById('editProductName').value = productName;
    document.getElementById('editQuantity').value = productQuantity;
    document.getElementById('editLocation').value = productLocation;
    document.getElementById('editSerialNumber').value = productSN; // Set Serial Number
    
    // unit dropdown
    const unitDropdown = document.getElementById('editUnit');
    for (let option of unitDropdown.options) {
        if (option.value === productQuantityUnit) {
            option.selected = true;
            break;
        }
    }
}


    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // search functionality
    document.getElementById('search-button').onclick = function() {
        performSearch();
    };

    document.getElementById('search-input').addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            event.preventDefault(); // Prevent form submission
            performSearch(); // Trigger search function
        }
    });

    function performSearch() {
        let input = document.getElementById('search-input').value.toLowerCase();
        let tableRows = document.querySelectorAll('.table tbody tr');

    tableRows.forEach(row => {
        let cells = row.getElementsByTagName('td');
        let rowContainsInput = Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(input));
        row.style.display = rowContainsInput ? '' : 'none';
    });
}


    // open the modal
    function openAddProductModal() {
        document.getElementById('addProductModal').style.display = 'block';
    }

    // close the modal
    function closeAddProductModal() {
        document.getElementById('addProductModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('addProductModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
}

    // Pass all products to JavaScript
    const allProducts = <?php echo json_encode($all_products); ?>;

    document.getElementById('search-button').onclick = function() {
        performSearch();
    };

    document.getElementById('search-input').addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            event.preventDefault(); // Prevent form submission
            performSearch(); // Trigger search function
        }
    });

    function performSearch() {
        let input = document.getElementById('search-input').value.toLowerCase();
        let tableBody = document.querySelector('.table tbody');
        tableBody.innerHTML = ''; // Clear existing rows

        allProducts.forEach(product => {
            // Check if the product matches the search input
            if (product.I_Product.toLowerCase().includes(input) || 
                (product.I_SN && product.I_SN.toString().includes(input)) || 
                product.I_Location.toLowerCase().includes(input)) {
                // Create a new row
                let newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>${product.I_ID}</td>
                    <td>${product.I_Product}</td>
                    <td>${(product.I_SN === 0) ? 'N/A' : product.I_SN}</td>
                    <td>${product.I_Quantity} ${product.I_Unit}</td>
                    <td>${product.I_Location}</td>
                    <td>
                        <button type="button" class="btn btn-primary" 
                            onclick="openEditModal(${product.I_ID}, '${product.I_Product}', '${product.I_Quantity}', '${product.I_Location}', '${product.I_Unit}', '${product.I_SN}')">Edit</button>              
                        <button type="button" class="btn btn-danger" onclick="openModal(${product.I_ID})">Delete</button>
                    </td>
                `;
                tableBody.appendChild(newRow);
            }
        });
    }
    function updateLimit(limit) {
        const url = new URL(window.location.href);
        url.searchParams.set('limit', limit); // Set the new limit in the query string
        url.searchParams.set('page', 1); // Reset to the first page
        window.location.href = url.toString(); // Reload the page
    }
        $(function () {
    $('[Logout"]').tooltip()
    })
</script>
</body>
</html>