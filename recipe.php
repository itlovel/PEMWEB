<?php
require 'db.php';

if (isset($_SESSION['user_id'])) {
    // Add Recipe
    if (isset($_POST['add_recipe'])) {
        $title = sanitize($_POST['title']);
        $ingredients = sanitize($_POST['ingredients']);
        $instructions = sanitize($_POST['instructions']);
        $user_id = $_SESSION['user_id'];
        
        // Insert recipe data first
        $query = "INSERT INTO recipes (user_id, title, ingredients, instructions) 
                  VALUES ('$user_id', '$title', '$ingredients', '$instructions')";
        mysqli_query($conn, $query);
        $recipe_id = mysqli_insert_id($conn);
        
        // Handle multiple image uploads
        $target_dir = "uploads/";
        foreach ($_FILES["images"]["name"] as $key => $image_name) {
            $target_file = $target_dir . basename($image_name);
            move_uploaded_file($_FILES["images"]["tmp_name"][$key], $target_file);
            
            // Save image path to database
            $image_query = "INSERT INTO recipe_images (recipe_id, image) 
                            VALUES ('$recipe_id', '$target_file')";
            mysqli_query($conn, $image_query);
        }

        header("Location: index.php?page=home");
    }

    // Update Recipe
if (isset($_POST['update_recipe'])) {
    $id = sanitize($_POST['id']);
    $title = sanitize($_POST['title']);
    $ingredients = sanitize($_POST['ingredients']);
    $instructions = sanitize($_POST['instructions']);
    
    $query = "UPDATE recipes SET title = '$title', ingredients = '$ingredients', instructions = '$instructions' 
              WHERE id = $id AND user_id = " . $_SESSION['user_id'];
    mysqli_query($conn, $query);

    // Handle new image uploads
    if (!empty($_FILES["images"]["name"][0])) {
        // Optionally delete old images
        $delete_old_images = "DELETE FROM recipe_images WHERE recipe_id = $id";
        mysqli_query($conn, $delete_old_images);

        foreach ($_FILES["images"]["name"] as $key => $image_name) {
            $target_file = "uploads/" . basename($image_name);
            move_uploaded_file($_FILES["images"]["tmp_name"][$key], $target_file);
            $image_query = "INSERT INTO recipe_images (recipe_id, image) 
                            VALUES ('$id', '$target_file')";
            mysqli_query($conn, $image_query);
        }
    }

    header("Location: index.php?page=home");
}
}
?>
