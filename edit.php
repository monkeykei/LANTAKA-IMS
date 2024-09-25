<?php
session_start();
require 'db.php'; 

function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $I_Product = sanitize_input($_POST['I_Product']);
    $I_Quantity = sanitize_input($_POST['I_Quantity']);
    $I_Price = sanitize_input($_POST['I_Price']);
    $I_ID = sanitize_input($_POST['I_ID']); 

    $sql = "UPDATE Inventory SET I_product=?, I_quantity=?, I_price=? WHERE I_ID=?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sidi", $I_Product, $I_Quantity, $I_Price, $I_ID); 
        if ($stmt->execute()) {
            header("Location: inventory.php");
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
    if (isset($_GET['I_ID'])) {
        $I_ID = $_GET['I_ID']; 
        $sql = "SELECT I_product, I_quantity, I_price FROM Inventory WHERE I_ID = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $I_ID); 
            $stmt->execute();
            $stmt->bind_result($product_name, $product_quantity, $product_price); 
            $stmt->fetch();
            $stmt->close();
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
            background-color: #ADD8E6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .signup-container {
            max-width: 500px;
            width: 100%;
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .signup-container h2 {
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
<div class="signup-container">
    <h2 class="text-center">Edit Product</h2>
    <a href="inventory.php" style="margin-top: -100px;" class="btn btn-secondary back-btn">Back</a>
    <form action="edit.php" method="post"> 

        
        <input type="hidden" name="I_ID" value="<?php echo $I_ID; ?>">

        <div class="form-group">
            <label for="Product">Product Name:</label>
            <input type="text" class="form-control" id="I_Product" name="I_Product" value="<?php echo $product_name; ?>" required>
        </div>
        <div class="form-group">
            <label for="Quantity">Quantity:</label>
            <input type="number" class="form-control" id="I_Quantity" name="I_Quantity" value="<?php echo $product_quantity; ?>" required>
        </div>
        <div class="form-group">
            <label for="Price">Price:</label>
            <input type="number" class="form-control" id="I_Price" name="I_Price" value="<?php echo $product_price; ?>" required>
        </div>

        <button type="submit" class="btn btn-success btn-block">Update Product</button>
    </form>
</div>
</body>
</html>
