<?php
session_start();
require 'db.php'; 

// Check if the delete form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    // Get the product ID to delete
    $product_id = $_POST['I_ID'];

    // Prepare the SQL statement to delete the product
    $delete_query = "DELETE FROM inventory WHERE I_ID = ?";
    
    if ($stmt = $conn->prepare($delete_query)) {
        // Bind the product ID to the statement
        $stmt->bind_param("i", $product_id);

        // Execute the query
        if ($stmt->execute()) {
            $_SESSION['message'] = "Product deleted successfully.";
        } else {
            $_SESSION['error'] = "Error deleting product: " . $conn->error;
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = "Error preparing delete query: " . $conn->error;
    }

    // Redirect to refresh the page after delete
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Retrieve the inventory items from the database
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
            background-color: #f5f5f5;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            text-align: center;
        }
        .close {
            color: black;
            float: right;
            font-size: 20px;
            font-weight: bold;
            height: 20px;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
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
            vertical-align: middle;
            text-align: center;
        }
        .table tbody + tbody {
            border-top: 2px solid #dee2e6;
        }
        .table td .btn {
            display: inline-block;
            vertical-align: middle;
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
                    <td> <?php echo $row['I_Quantity']; ?> </td>
                    <td> <?php echo $row['I_Location']; ?> </td>
                    <td>
                        <a href="edit.php?I_ID=<?php echo $row['I_ID']; ?>" class="btn btn-primary">Edit</a>
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

<!-- Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Are you sure you want to delete this product?</p>
        <form method="post" action="">
            <input type="hidden" id="deleteProductId" name="I_ID" value="">
            <button type="submit" name="delete" class="btn btn-danger">Yes</button>
            <button type="button" class="btn btn-light" onclick="closeModal()">No</button>
        </form>
    </div>
</div>

<script>
var modal = document.getElementById("deleteModal");
var span = document.getElementsByClassName("close")[0];

function openModal(productId) {
    modal.style.display = "block";
    document.getElementById("deleteProductId").value = productId;
}

function closeModal() {
    modal.style.display = "none";
}

span.onclick = function() {
    closeModal();
}

window.onclick = function(event) {
    if (event.target == modal) {
        closeModal();
    }
}
</script>

</body>
</html>
