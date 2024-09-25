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
            // Success message (optional)
            $_SESSION['message'] = "Product deleted successfully!";
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
</head>
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
        max-width: 1000px;
        width: 100%;
        background-color: #ffffff;
        padding: 2rem;
        border-radius: 0.5rem;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        height: 500px;
    }
    .signup-container h2 {
        margin-bottom: 1.5rem;
    }
    .active {
        background-color: #087cfc;
        color: white;
    }
    .back-btn {
        margin-top: -30px;
        margin-bottom: 20px;
    }
    #logout{
        margin-top: -15%;
        margin-right: -90%;
    }
</style>
<body>
<div class="signup-container">
    <h2 class="text-center">Inventory</h2>
    <a href="Menu.php" style="margin-top: -80px;" class="btn btn-secondary back-btn">Back</a>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']); //unset - prevent from being reused/clear session
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

    <center> 
    <button type="button" id="logout" class="btn btn-danger btn-lg" onclick="document.location='Index.php'">Logout</button>  
        <table class="table">
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
                        <!-- Form for deleting the product -->
                        <form method="post" action="">
                            <input type="hidden" name="I_ID" value="<?php echo $row['I_ID']; ?>">
                            <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>  
    </center>
</div>
</body>
</html>
