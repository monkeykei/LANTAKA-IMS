<?php
session_start();
require 'db.php';

//SANITIZE INPUT DATA
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $U_Username = sanitize_input($_POST['username']);
    $U_Password = password_hash(sanitize_input($_POST['password']), PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (U_Username, U_Password) VALUES ('$U_Username', '$U_Password')";

    if ($conn->query($sql) === TRUE) {
        $success_message = "Registration successful. <a href='index.php'>Login here</a>";
        header('Location: index.php');
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
    <title>Sign Up</title>
    <title>ADZU Create User</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
        body {
            background: url('bg_pic.png') no-repeat center center fixed;
            background-size: cover;
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
<h2 class="text-center">Create New User</h2>
<form action="signup.php" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-success btn-block">Register</button>
</form>
</div>
</body>
</html>
