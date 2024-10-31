<?php
$conn = mysqli_connect("localhost", "root", "", "food_recipe_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!function_exists('sanitize')) {
    function sanitize($data) {
        global $conn;
        return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
    }
}
?>