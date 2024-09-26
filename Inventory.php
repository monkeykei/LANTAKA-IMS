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
        } else {
            $_SESSION['error'] = "Error deleting product: " . $conn->error;
        }

        $stmt->close();
    }
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
    <style>
        body {
            background-color: #ADD8E6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .signup-container {
            max-width: 1500px;
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
            margin-bottom: 1rem;
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
    <h2 class="text-center">Inventory</h2>

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
        <button type="button" class="btn btn-danger btn-lg" onclick="document.location='Index.php'">Logout</button>      
    </div>

    <div class="table-container">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col" class="active">ID</th>
                    <th scope="col" class="active">Product</th>
                    <th scope="col" class="active">Quantity</th>
                    <th scope="col" class="active">Price</th>
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
                    <td> <?php echo $row['I_Price']; ?> </td>
                    <td>
                        <a href="edit.php?I_ID=<?php echo $row['I_ID']; ?>" class="btn btn-primary">Edit</a>
                    </td>
                    <td>
                        <form method="post" action="">
                            <input type="hidden" name="I_ID" value="<?php echo $row['I_ID']; ?>">
                            <button type="button" id="openModal-<?php echo $row['I_ID']; ?>" class="btn btn-danger">Delete</button>

                            <div id="modal-<?php echo $row['I_ID']; ?>" class="modal">
                                <div class="modal-content">
                                    <span class="close" data-modal-id="<?php echo $row['I_ID']; ?>">&times;</span>
                                    <p>Are you sure you want to delete this product?</p>
                                    <button type="submit" name="delete" class="btn btn-danger">Yes</button>
                                    <button type="button" class="btn btn-light close" data-modal-id="<?php echo $row['I_ID']; ?>">No</button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>  
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php foreach ($result as $row) { ?>
        var modal = document.getElementById("modal-<?php echo $row['I_ID']; ?>");
        var btn = document.getElementById("openModal-<?php echo $row['I_ID']; ?>");
        var closeModalButtons = document.querySelectorAll('[data-modal-id="<?php echo $row['I_ID']; ?>"]');
        
        btn.onclick = function() {
            modal.style.display = "block";
        }

        closeModalButtons.forEach(function(btn) {
            btn.onclick = function() {
                modal.style.display = "none";
            }
        });
    <?php } ?>

    // Close the modal if the user clicks outside of it
    window.onclick = function(event) {
        <?php foreach ($result as $row) { ?>
            var modal = document.getElementById("modal-<?php echo $row['I_ID']; ?>");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        <?php } ?>
    }
});
</script>

</body>
</html>