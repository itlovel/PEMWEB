<?php
require 'auth.php';
require 'recipe.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Recipe Sharing Platform</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .recipe { margin-bottom: 20px; }
        img { max-width: 100%; height: auto; }
        nav { margin-bottom: 30px; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Recipe Platform</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=home">Home</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=add_recipe">Add Recipe</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="auth.php?logout">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success" role="alert"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($page == 'home'): ?>
            <h1 class="text-center mb-5">Latest Recipes</h1>
            <div class="row">
                <?php
                $query = "SELECT r.*, u.username FROM recipes r JOIN users u ON r.user_id = u.id ORDER BY r.id DESC";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)):
                    $recipe_id = $row['id'];
                    $image_query = "SELECT * FROM recipe_images WHERE recipe_id = $recipe_id";
                    $image_result = mysqli_query($conn, $image_query);
                ?>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $row['title']; ?></h5>
                                <p class="card-text">By: <?php echo $row['username']; ?></p>
                                
                                <!-- Display multiple images -->
                                <div class="mb-3">
                                    <?php while ($image = mysqli_fetch_assoc($image_result)): ?>
                                        <img src="<?php echo $image['image']; ?>" alt="Recipe Image" class="img-fluid mb-2">
                                    <?php endwhile; ?>
                                </div>

                                <h6>Ingredients:</h6>
                                <p><?php echo nl2br($row['ingredients']); ?></p>
                                <h6>Instructions:</h6>
                                <p><?php echo nl2br($row['instructions']); ?></p>

                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']): ?>
                                    <a href="index.php?page=edit_recipe&id=<?php echo $row['id']; ?>" class="btn btn-primary">Edit</a>
                                    <a href="index.php?delete_recipe=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

        <?php elseif ($page == 'login'): ?>
            <h1 class="text-center mb-5">Login</h1>
            <form method="post" class="mx-auto" style="max-width: 400px;">
                <div class="mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <div class="d-grid">
                    <input type="submit" name="login" value="Login" class="btn btn-primary">
                </div>
            </form>

        <?php elseif ($page == 'register'): ?>
            <h1 class="text-center mb-5">Register</h1>
            <form method="post" class="mx-auto" style="max-width: 400px;">
                <div class="mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="d-grid">
                    <input type="submit" name="register" value="Register" class="btn btn-primary">
                </div>
            </form>

        <?php elseif ($page == 'add_recipe'): ?>
            <h1 class="text-center mb-5">Add New Recipe</h1>
            <form method="post" enctype="multipart/form-data" class="mx-auto" style="max-width: 600px;">
                <div class="mb-3">
                    <input type="text" name="title" class="form-control" placeholder="Recipe Title" required>
                </div>
                <div class="mb-3">
                    <textarea name="ingredients" class="form-control" placeholder="Ingredients (one per line)" required></textarea>
                </div>
                <div class="mb-3">
                    <textarea name="instructions" class="form-control" placeholder="Instructions" required></textarea>
                </div>
                <div class="mb-3">
                    <input type="file" name="images[]" multiple class="form-control" required>
                </div>
                <div class="d-grid">
                    <input type="submit" name="add_recipe" value="Add Recipe" class="btn btn-success">
                </div>
            </form>

        <?php elseif ($page == 'edit_recipe' && isset($_GET['id'])): ?>
            <h1 class="text-center mb-5">Edit Recipe</h1>
            <?php
            $id = sanitize($_GET['id']);
            $query = "SELECT * FROM recipes WHERE id = $id AND user_id = " . $_SESSION['user_id'];
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) == 1):
                $recipe = mysqli_fetch_assoc($result);
            ?>
                <form method="post" enctype="multipart/form-data" class="mx-auto" style="max-width: 600px;">
                    <input type="hidden" name="id" value="<?php echo $recipe['id']; ?>">
                    <div class="mb-3">
                        <input type="text" name="title" class="form-control" value="<?php echo $recipe['title']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <textarea name="ingredients" class="form-control" required><?php echo $recipe['ingredients']; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <textarea name="instructions" class="form-control" required><?php echo $recipe['instructions']; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Existing Images:</label>
                        <div class="row">
                            <?php
                            $image_query = "SELECT * FROM recipe_images WHERE recipe_id = $id";
                            $image_result = mysqli_query($conn, $image_query);
                            while ($image = mysqli_fetch_assoc($image_result)):
                            ?>
                                <div class="col-md-4">
                                    <img src="<?php echo $image['image']; ?>" alt="Recipe Image" class="img-fluid mb-2">
                                    <input type="checkbox" name="delete_images[]" value="<?php echo $image['id']; ?>"> Delete
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="file" name="images[]" multiple class="form-control">
                    </div>
                    <div class="d-grid">
                        <input type="submit" name="update_recipe" value="Update Recipe" class="btn btn-primary">
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-danger">Recipe not found or you are not authorized to edit it.</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
