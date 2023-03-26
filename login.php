<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: ./index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="src/auth-forms.css">
    <title>Login</title>
</head>

<body>
    <div class="auth-forms">
        <h1>Login</h1>
        <form action="actions/login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login" value="Login"><i class="fa-solid fa-right-to-bracket"></i> Login</button>
            <a href="register.php">You don't have an account? Register</a>
        </form>
        <?php
        if (isset($_GET['error'])) {
            if ($_GET['error'] == 'user_not_exists') {
                echo '<p class="error">User not found</p>';
            } else if ($_GET['error'] == 'wrong_password') {
                echo '<p class="error">Wrong password</p>';
            }
        }
        ?>
    </div>
    <script src="https://kit.fontawesome.com/4ee017f251.js" crossorigin="anonymous"></script>
</body>

</html>