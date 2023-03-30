<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1.0, width=device-width">
    <title><?php echo $_SESSION['user']['name']; ?></title>
    <script src="https://kit.fontawesome.com/4ee017f251.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="src/profile.css">

</head>

<body>

    <header>
        <navbar>
            <a href="logout.php">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>
                    Logout
                </span>
            </a>
            <a href="index.php">
                <i class="fa-solid fa-home"></i>
                <span>
                    Home
                </span>
            </a>
            <div>
                <span>
                    <?php echo $_SESSION['user']['name']; ?>
                </span>
                <?php if (isset($_SESSION['user']['image'])) : ?>
                    <img src="image.php?filename=<?php echo $_SESSION['user']['image']; ?>" alt="Profile image">
                <?php else : ?>
                    <img src="./public/images/default.jpg" alt="Profile image">
                <?php endif; ?>

            </div>
        </navbar>
    </header>
    <?php if (isset($_GET['error'])) : ?>
        <div class="error">
            <?php
            switch ($_GET['error']) {
                case 'no_changes':
                    echo 'No changes were made';
                    break;
                case 'empty_name':
                    echo 'Name cannot be empty';
                    break;
                case 'invalid_image_type':
                    echo 'Image must be a valid image';
                    break;
                case 'invalid_image_size':
                    echo 'Image must be less than 2MB';
                    break;
                case 'invalid_image':
                    echo 'There was an error uploading the image';
                    break;
                case 'invalid_password':
                    echo "The password you entered is incorrect";
                    break;
                case 'passwords_do_not_match':
                    echo "Passwords do not match";
                    break;
                default:
                    echo 'Something went wrong';
                    break;
            }
            ?>
        </div>

    <?php endif; ?>

    <?php if (isset($_GET['success'])) : ?>
        <div class="success">
            <?php
            switch ($_GET['success']) {
                case 'password_changed':
                    echo 'Password changed successfully';
                    break;
                case 'profile_updated':
                    echo 'Profile updated successfully';
                    break;
                default:
                    echo 'Something went wrong';
                    break;
            }
            ?>
        </div>
    <?php endif; ?>
    <main>


        <!-- Update profile (image, name) -->

        <?php
        $image = isset($_SESSION['user']['image']) ? $_SESSION['user']['image'] : null;
        ?>
        <div class="part">
            <form action="actions/update_profile.php" method="POST" enctype="multipart/form-data">
                <label class="profile-image-container" aria-label="Upload profile image">
                    <?php if ($image) : ?>
                        <img src="image.php?filename=<?php echo $_SESSION['user']['image']; ?>" alt="Profile image" class="profile-image">
                    <?php else : ?>
                        <img src="./public/images/default.jpg" alt="Profile image" class="profile-image">
                    <?php endif; ?>
                    <i class="fa-solid fa-camera" title="Upload image"></i>
                    <input type="file" name="image" id="image" title="Choose image">
                </label>
                <div>
                    <label for="name">Name</label>
                    <input type="text" name="name" class="profile-name" value="<?php echo $_SESSION['user']['name']; ?>">
                    <input type="submit" value="Update">
                </div>
            </form>
        </div>


        <!-- Change password area -->

        <div class="part">
            <h2>Change password</h2>
            <form action="actions/change_password.php" method="POST">
                <label for="current_password">Current password</label>
                <input type="password" name="current_password" id="current_password">
                <label for="new_password">New password</label>
                <input type="password" name="new_password" id="new_password">
                <label for="confirm_password">Confirm password</label>
                <input type="password" name="confirm_password" id="confirm_password">
                <input type="submit" value="Change password">
            </form>
        </div>

        <!-- Delete Account -->

        <div class="part">
            <h2>Delete account</h2>
            <form action="actions/delete_account.php" method="POST">
                <label for="password">Password</label>
                <input type="password" name="password" id="password">
                <input type="submit" value="Delete account">
            </form>
        </div>



        <script src="src/profile.js"></script>

    </main>


</body>

</html>