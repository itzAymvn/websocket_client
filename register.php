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
    <title>Register</title>
</head>

<body>
    <div class="auth-forms">
        <h1>Register</h1>
        <form action="actions/register.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="register" value="Register">Register <i class="fa-solid fa-plus"></i></button>
            <a href="login.php">Already have an account? Login</a>

        </form>

        <?php
        if (isset($_GET['error'])) {
            if ($_GET['error'] == 'user_exists') {
                echo '<p class="error">Username already exists</p>';
            }
        }
        ?>

    </div>
    <script src="https://kit.fontawesome.com/4ee017f251.js" crossorigin="anonymous"></script>

</body>

</html>