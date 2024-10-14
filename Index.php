<?php
session_start();
require 'db.php';

//SANITIZE INPUT DATA
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $U_Username = sanitize_input($_POST['username']);
    $U_Password = sanitize_input($_POST['password']);

    $sql = "SELECT * FROM users WHERE U_Username = '$U_Username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($U_Password, $row['U_Password'])) {
            $_SESSION['loggedin'] = true;
            header('Location: Inventory.php');
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "No user found with that username.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADZU LANTAKA Inventory Management System</title>
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
        .login-container {
            width: 600px;
            height: 500px;           
            padding: 3rem;
            border-radius: 0.5rem;           
            display: flex;
            flex-direction: column;
            justify-content: center;
            transform: translateY(-50px); 
        }
        .login-container h2 {
            margin-bottom: 2rem;
            font-size: 2.5rem;
        }
        .form-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        .form-control {
            font-size: 1.5rem;
            padding: 1rem;
            font-family: 'Verdana', monospace;
        }
        .btn-primary {
            font-size: 1.2rem;
            padding: 0.75rem;
        }
        .mt-4 {
            margin-top: 2rem;
        }
        @media (max-width: 768px) {
            .login-container {
                width: 100%;
                height: auto;
                min-height: 600px;
                padding: 2rem;
            }
            .login-container h2 {
                font-size: 2rem;
            }
        }
        img{
            max-height: 10rem;
            max-width: 10rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="form-container">
            <center><img src="adzu_logo.png" alt="ateneo"></center>
            <h2 class="text-center" style="color: white; font-family: 'Montserrat', sans-serif;">ADZU LANTAKA <br> Inventory Management System</h2>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            <form action="index.php" method="post">
                <div class="form-group">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form> 
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
