<?php
session_start();
require 'db.php';

// User Authentication
if (isset($_POST['login'])) {
    $username = sanitize($_POST['username']);
    $password = sanitize($_POST['password']);
    
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php?page=home");
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "User not found";
    }
}

// User Registration
if (isset($_POST['register'])) {
    $username = sanitize($_POST['username']);
    $password = password_hash(sanitize($_POST['password']), PASSWORD_DEFAULT);
    $email = sanitize($_POST['email']);
    
    $query = "INSERT INTO users (username, password, email) VALUES ('$username', '$password', '$email')";
    if (mysqli_query($conn, $query)) {
        $success = "Registration successful. Please login.";
    } else {
        $error = "Registration failed: " . mysqli_error($conn);
    }
}

// User Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
}
?>
