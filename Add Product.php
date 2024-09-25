<?php
session_start();
require 'db.php';

//SANITIZE INPUT DATA
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  
    $I_Product = sanitize_input($_POST['I_Product']);
    $I_Quantity = sanitize_input(($_POST['I_Quantity']));
    $I_Price = sanitize_input($_POST['I_Price']);

    $sql = "INSERT INTO Inventory (I_Product, I_Quantity, I_Price) VALUES ('$I_Product', '$I_Quantity', '$I_Price')";

    if ($conn->query($sql) === TRUE) {
        $success_message = "Registration successful. <a href='Inventory.php'>Go to inventory</a>";
    } else {
        $error_message = "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <title>Inventory Management System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
        body {
            background-color: #f8f9fa;
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
<body>
<div class="signup-container">
<h2 class="text-center">Add a Product</h2>
<a href="Menu.php" style="margin-top: -80px;" class="btn btn-secondary back-btn">Back</a>
<form action="add product.php" method="post">
            <div class="form-group">
                <label for="Product">Product name:</label>
                <input type="text" class="form-control" id="I_Product" name="I_Product" required>
            </div>
            <div class="form-group">
                <label for="Quantity">Quantity:</label>
                <input type="number" class="form-control" id="I_Quantity" name="I_Quantity" required>
            </div>
            <div class="form-group">
                <label for="Price">Price:</label>
                <input type="number" class="form-control" id="I_Price" name="I_Price" required>
            </div>
            <button type="submit" class="btn btn-success btn-block">Submit</button>
</form>
</div>
</body>
</html>
