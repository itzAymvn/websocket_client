<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ./login.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="src/style.css">
    <title>Chat </title>
</head>

<body>

    <div class="connecting_loading show" id="connecting_loading">
        <div class="connecting_loading__content">
            <i class="fa-solid fa-spinner"></i>
        </div>
    </div>
    <div class="connectedUsers" id="connectedUsers">
        <h2>
            <span>
                Connected Users
            </span>
            <i class="fa-solid fa-times" id="close_connected_menu"></i>
        </h2>
        <div id="users"></div>
    </div>
    <header>
        <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"> Logout</i></a>
        <div id="connectedUsers__btn" class="connectedUsers__btn"><span id="connected"></span> <i class="fa-solid fa-user"></i></div>
        <a href="#" class="profile" onclick="alert('Profile feature is not available yet!')">
            <?php
            echo $_SESSION['user']['name'];
            ?> <i class="fa-solid fa-user"></i>
        </a>
    </header>
    <div class="messages" id="messages">

    </div>


    <form class="form">
        <input type="text" name="message" id="message" placeholder="Type a message...">
        <button id="send">Send <i class="fa-solid fa-play"></i></button>
    </form>

    <?php
    if (isset($_SESSION['user'])) {
        echo '<script>';
        echo 'const user = ' . json_encode([
            'id' => $_SESSION['user']['id'],
            'name' => $_SESSION['user']['name'],
            'created_at' => $_SESSION['user']['created_at']
        ]) . ';';
        echo '</script>';
    }
    ?>
    <script src="https://kit.fontawesome.com/4ee017f251.js" crossorigin="anonymous"></script>
    <script type="module" src="src/script.js"></script>

</body>

</html>