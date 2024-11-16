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
    <link rel="stylesheet" href="CSS/CSS_Signup.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Roboto Slab' rel='stylesheet'>
    <style>
        body {
            background: url('pictures/signup_bg.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
    </style>
</head>
<body>
<div class="signup-container">
<h2 class="text-center" style="font-family: 'Roboto slab', sans-serif;  font-size: 40px">Create New User</h2>
<form action="signup.php" method="post">
<div class="form-group">
            <div class="input-group">
                <div class="input-group-item">
                    <label for="firstname">First Name</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" placeholder="First Name" required>
                </div>
                <div class="input-group-item">
                    <label for="mi">M.I.</label>
                    <input type="text" class="form-control" id="mi" name="mi" placeholder="M.I" required style="width: 60px;">
                </div>
                <div class="input-group-item">
                    <label for="lastname">Last Name</label>
                    <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Last Name" required>
                </div>
            </div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <label for="password">Confirm Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Confirm Password" required>
            </div>
            <button type="submit" class="btn btn-success btn-block">Register</button>
</form>
</div>
</body>
</html>
