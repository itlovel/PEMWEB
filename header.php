<nav>
    <a href="index.php?page=home">Home</a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="index.php?page=add_recipe">Add Recipe</a>
        <a href="auth.php?logout">Logout</a>
    <?php else: ?>
        <a href="index.php?page=login">Login</a>
        <a href="index.php?page=register">Register</a>
    <?php endif; ?>
</nav>
