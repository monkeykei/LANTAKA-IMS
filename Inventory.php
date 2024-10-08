<?php
session_start();
require 'db.php'; 

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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
    $I_ID = $_POST['I_ID'];
    $I_Product = $_POST['I_Product'];
    $I_Quantity = $_POST['I_Quantity'];
    $I_Location = $_POST['I_Location'];
    $I_Unit = $_POST['I_Unit'];

    $edit_query = "UPDATE inventory SET I_Product = ?, I_Quantity = ?, I_Location = ?, I_Unit = ? WHERE I_ID = ?";
    
    if ($stmt = $conn->prepare($edit_query)) {
        $stmt->bind_param("sissi", $I_Product, $I_Quantity, $I_Location, $I_Unit, $I_ID);
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

$query = "SELECT * FROM inventory";
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
    <style>
        body {
            background: url('bg_pic.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .button-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .search-bar {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .signup-container {
            max-width: 1400px;
            width: 100%;
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }
        .signup-container h2 {
            margin-bottom: 1.5rem;
        }
        .active {
            background-color: #087cfc;
            color: white;
        }
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            padding-top: 100px; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            background-color: rgba(0,0,0,0.4); 
        }
        .modal-content {
            position: relative; 
            background-color: #f5f5f5;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            text-align: center;
        }

        .close {
            position: absolute;
            top: 10px; 
            right: 15px;
            color: black;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black; 
            text-decoration: none;
        }

        .table-container {
            overflow-x: auto;
        }
        .table {
            border-collapse: separate;
            border-spacing: 0;
        }
        .table th,
        .table td {
            border: 1px solid #dee2e6;
            vertical-align: middle;
            text-align: center;
            padding: 0.75rem;
        }
        .table thead th {
            border-bottom: 2px solid #dee2e6;
        }
    </style>
</head>
<body>
<div class="signup-container">
    <h2 class="text-center">House Keeping Inventory</h2>

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
        <button type="button" class="btn btn-success btn-lg" onclick="document.location='Add Product.php'">Add Product</button>

        <div class="search-bar">
            <input id="search-input" type="search" class="form-control" placeholder="Search for a product"/>
            <button id="search-button" type="button" class="btn btn-primary">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <button type="button" class="btn btn-danger btn-lg" onclick="document.location='Index.php'">Logout</button>      
    </div>

    <div class="table-container">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col" class="active">ID</th>
                    <th scope="col" class="active">Item Description</th>
                    <th scope="col" class="active">Serial No.</th>
                    <th scope="col" class="active">Units</th>
                    <th scope="col" class="active">Located at</th>
                    <th scope="col" class="active"></th>
                    <th scope="col" class="active"></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) {  ?>          
                <tr>
                    <td> <?php echo $row['I_ID']; ?> </td> 
                    <td> <?php echo $row['I_Product']; ?> </td>
                    <td> <?php echo ($row['I_SN'] == 0) ? 'N/A' : $row['I_SN']; ?> </td>
                    <td> <?php echo $row['I_Quantity'] . ' ' . $row['I_Unit']; ?> </td>
                    <td> <?php echo $row['I_Location']; ?> </td>
                    <td>
                        <button type="button" class="btn btn-primary" onclick="openEditModal(<?php echo $row['I_ID']; ?>, '<?php echo $row['I_Product']; ?>', '<?php echo $row['I_Quantity']; ?>', '<?php echo $row['I_Location']; ?>', '<?php echo $row['I_Unit']; ?>')">Edit</button>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger" onclick="openModal(<?php echo $row['I_ID']; ?>)">Delete</button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>  
    </div>
</div>

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

            <div class="form-group">
                <label for="editQuantity">Quantity:</label>
                <input type="number" class="form-control" id="editQuantity" name="I_Quantity" value="" required>
            </div>

            <div class="form-group">
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

            <div class="form-group">
                <label for="editLocation">Located at:</label>
                <input type="text" class="form-control" id="editLocation" name="I_Location" value="" required>
            </div>

            <button type="submit" name="edit" class="btn btn-success">Save Changes</button>
        </form>
    </div>
</div>

<script>
    function openModal(productId) {
        document.getElementById('deleteModal').style.display = 'block';
        document.getElementById('deleteProductId').value = productId;
    }

    function closeModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    function openEditModal(productId, productName, productQuantity, productLocation, productQuantityUnit) {
        document.getElementById('editModal').style.display = 'block';
        document.getElementById('editProductId').value = productId;
        document.getElementById('editProductName').value = productName;
        document.getElementById('editQuantity').value = productQuantity;
        document.getElementById('editLocation').value = productLocation;
        
        // Set the unit dropdown
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

    // Implement search functionality
    document.getElementById('search-button').onclick = function() {
        let input = document.getElementById('search-input').value.toLowerCase();
        let tableRows = document.querySelectorAll('.table tbody tr');

        tableRows.forEach(row => {
            let cells = row.getElementsByTagName('td');
            let rowContainsInput = Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(input));
            row.style.display = rowContainsInput ? '' : 'none';
        });
    };
</script>
</body>
</html>
