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
    <link rel="stylesheet" href="src/index.css">
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
        <a href="profile.php" class="profile">
            <?php echo $_SESSION['user']['name']; ?>
            <?php if (isset($_SESSION['user']['image'])) : ?>
                <img src="image.php?filename=<?php echo $_SESSION['user']['image']; ?>" alt="Profile image" class="profile__image">
            <?php else : ?>
                <img src="./public/images/default.jpg" alt="Profile image" class="profile__image">
            <?php endif; ?>
        </a>

    </header>
    <main>
        <div class="messages" id="messages">

        </div>


        <form class="form">
            <input type="text" name="message" id="message" placeholder="Type a message...">
            <button id="send">Send <i class="fa-solid fa-play"></i></button>
        </form>
    </main>

    <?php
    if (isset($_SESSION['user'])) : ?>
        <script>
            const user = <?php echo json_encode([
                                'id' => $_SESSION['user']['id'],
                                'name' => $_SESSION['user']['name'],
                                'created_at' => $_SESSION['user']['created_at'],
                                'image' => $_SESSION['user']['image'] ?? null
                            ]); ?>;
        </script>
    <?php endif; ?>

    <script src="https://kit.fontawesome.com/4ee017f251.js" crossorigin="anonymous"></script>
    <script type="module" src="src/script.js"></script>

</body>

</html>