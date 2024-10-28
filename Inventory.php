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
    <style>
        body {
            background: url('inventory_Bg.png') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column; /* Added to ensure vertical layout */
        }

        .content {
            margin: 0 auto; /* Center content horizontally */
            padding: 20px;
            width: 100%;
            flex: 1; /* Allow content to grow */
        }

        .signup-container {
            max-width: 1300px;
            width: 100%;
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            border: 5px solid #003a6c;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .table-container {
            overflow-x: auto;
        }

        .active {
            background-color: #087cfc;
            color: white;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        img {
            max-height: 3rem;
            max-width: 3rem;
        }
        .nav-link{
            color:white;
        }
        
    </style>
</head>
<body>
<!--Top Navbar-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid d-flex justify-content-between">
        <div class="d-flex align-items-center">
            <img src="adzu_logo.png" alt="Logo" style="height: 50px;"> 
                <h3 style="padding-left: 10px; padding-right: 10px; color: white">Lantaka Campus</h3>
        </div>
            <a class="nav-link" href="#">Logout</a>
        </div>
    </div>
</nav>

<!--Main-->
    <div class="content">
        <form class="signup-container">
            <h2 class="text-center" style="font-weight: bold; font-size: 50px">House Keeping Inventory</h2>

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
                    
                        <tr>
                            <th class="active">Product</th>
                            <th class="active">Quantity</th>
                            <th class="active">SN</th>
                            <th class="active">Location</th>
                            <th class="active">Unit</th>
                            <th class="active">Action</th>
                        </tr>
                    
                    <tbody id="product-table-body">
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['I_Product']; ?></td>
                                <td><?php echo $row['I_Quantity']; ?></td>
                                <td><?php echo $row['I_SN']; ?></td>
                                <td><?php echo $row['I_Location']; ?></td>
                                <td><?php echo $row['I_Unit']; ?></td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="openEditModal(<?php echo $row['I_ID']; ?>, '<?php echo $row['I_Product']; ?>', <?php echo $row['I_Quantity']; ?>, '<?php echo $row['I_SN']; ?>', '<?php echo $row['I_Location']; ?>', '<?php echo $row['I_Unit']; ?>')">Edit</button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="I_ID" value="<?php echo $row['I_ID']; ?>">
                                        <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="btn btn-light"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        </form>
    </div>
    
    <script>
        function openAddProductModal() {
            // Add your modal opening logic here
        }
        function openEditModal(I_ID, I_Product, I_Quantity, I_SN, I_Location, I_Unit) {
            // Add your edit modal opening logic here
        }
    </script>
</body>
</html>
