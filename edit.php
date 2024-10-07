<?php
session_start();
require 'db.php'; 

// Sanitize input to prevent XSS attacks
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the sanitized POST data
    $I_Product = sanitize_input($_POST['I_Product']);
    $I_Quantity = sanitize_input($_POST['I_Quantity']);
    $I_Location = sanitize_input($_POST['I_Location']);
    $I_ID = sanitize_input($_POST['I_ID']); 

    // SQL query for updating the inventory
    $sql = "UPDATE Inventory SET I_product=?, I_quantity=?, I_Location=? WHERE I_ID=?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sids", $I_Product, $I_Quantity, $I_Location, $I_ID); 
        if ($stmt->execute()) {
            header("Location: inventory.php"); // Redirect to the inventory page after update
            exit();
        } else {
            echo "Error updating record: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error preparing the query: " . $conn->error;
    }

    $conn->close();
} else {
    // Initialize default values to avoid "undefined variable" errors
    $I_Product = '';
    $I_Quantity = '';
    $I_Location = '';

    // Fetch product information when I_ID is passed via GET request
    if (isset($_GET['I_ID'])) {
        $I_ID = $_GET['I_ID']; 
        $sql = "SELECT I_product, I_quantity, I_Location FROM Inventory WHERE I_ID = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $I_ID); 
            $stmt->execute();
            $stmt->bind_result($product_name, $product_quantity, $I_Location); 
            
            // Fetch product data if available
            if ($stmt->fetch()) {
                $I_Product = $product_name;
                $I_Quantity = $product_quantity;
            } else {
                echo "No product found with that ID.";
                exit;
            }

            $stmt->close();
        } else {
            echo "Error preparing the query: " . $conn->error;
            exit;
        }
    } else {
        echo "Error: Product ID is not set.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
        .edit-container {
            width: 800px;
            height: 600px;
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
        }
        .edit-container h2 {
            margin-bottom: 2rem;
            font-size: 2.5rem;
            text-align: center;
        }
        .form-group label {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }
        .form-control {
            font-size: 1.1rem;
            padding: 0.75rem;
        }
        .btn-success {
            font-size: 1.2rem;
            padding: 0.75rem;
            margin-top: 1rem;
        }
        .back-btn {
            align-self: flex-start;
            margin-bottom: 1rem;
        }
        @media (max-width: 850px) {
            .edit-container {
                width: 100%;
                height: auto;
                min-height: 600px;
            }
        }
    </style>
</head>
<body>
<div class="edit-container">
    <a href="inventory.php" class="btn btn-secondary back-btn">Back</a>
    <h2>Edit Product</h2>
    <form action="edit.php" method="post"> 
        <input type="hidden" name="I_ID" value="<?php echo $I_ID; ?>">

        <!-- Product Name (read-only) -->
        <div class="form-group">
            <label for="Product">Product Name:</label>
            <input type="text" class="form-control" id="I_Product" name="I_Product" value="<?php echo $I_Product; ?>" readonly>
        </div>

        <!-- Quantity (editable) -->
        <div class="form-group">
            <label for="Quantity">Quantity:</label>
            <input type="number" class="form-control" id="I_Quantity" name="I_Quantity" value="<?php echo $I_Quantity; ?>" required>
        </div>

        <!-- Location (editable) -->
        <div class="form-group">
            <label for="I_Location">Located at:</label>
            <input type="text" class="form-control" id="I_Location" name="I_Location" value="<?php echo $I_Location; ?>" required>
        </div>

        <button type="submit" class="btn btn-success btn-block">Update Product</button>
    </form>
</div>
</body>
</html>
