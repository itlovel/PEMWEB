<?php
require_once 'auth.php';
require_once 'db.php'; 

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

if ($page == 'edit_recipe' && isset($_GET['id'])):
    $id = sanitize($_GET['id']);
    $query = "SELECT * FROM recipes WHERE id = $id AND user_id = " . $_SESSION['user_id'];
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1):
        $recipe = mysqli_fetch_assoc($result);

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_recipe'])):
            $title = sanitize($_POST['title']);
            $ingredients = sanitize($_POST['ingredients']);
            $instructions = sanitize($_POST['instructions']);
        
            $update_query = "UPDATE recipes SET title = '$title', ingredients = '$ingredients', instructions = '$instructions' WHERE id = $id AND user_id = " . $_SESSION['user_id'];
            mysqli_query($conn, $update_query);
        
            if (!empty($_FILES['images']['name'][0])) {
                foreach ($_FILES['images']['name'] as $key => $image_name) {
                    $image_tmp_name = $_FILES['images']['tmp_name'][$key];
                    $image_path = 'uploads/' . $image_name;
                    move_uploaded_file($image_tmp_name, $image_path);
        
                    $image_insert_query = "INSERT INTO recipe_images (recipe_id, image) VALUES ($id, '$image_path')";
                    mysqli_query($conn, $image_insert_query);
                }
            }
        
            if (isset($_POST['delete_images'])) {
                foreach ($_POST['delete_images'] as $image_id) {
                    $delete_image_query = "DELETE FROM recipe_images WHERE id = $image_id";
                    mysqli_query($conn, $delete_image_query);
                }
            }

            header('Location: index.php');
            exit();
        endif; 
    else:
        echo "<div class='alert alert-danger'>Recipe not found or you are not authorized to edit it.</div>";
    endif;

elseif ($page == 'add_recipe'):  
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_recipe'])):
        $title = sanitize($_POST['title']);
        $ingredients = sanitize($_POST['ingredients']);
        $instructions = sanitize($_POST['instructions']);
        $user_id = $_SESSION['user_id'];
    
        $insert_query = "INSERT INTO recipes (title, ingredients, instructions, user_id) VALUES ('$title', '$ingredients', '$instructions', $user_id)";
        mysqli_query($conn, $insert_query);
        $recipe_id = mysqli_insert_id($conn);
    
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['name'] as $key => $image_name) {
                $image_tmp_name = $_FILES['images']['tmp_name'][$key];
                $image_path = 'uploads/' . $image_name;
                move_uploaded_file($image_tmp_name, $image_path);
    
                $image_insert_query = "INSERT INTO recipe_images (recipe_id, image) VALUES ($recipe_id, '$image_path')";
                mysqli_query($conn, $image_insert_query);
            }
        }
    
        header('Location: index.php');
        exit();
    endif;     
endif;
?>




