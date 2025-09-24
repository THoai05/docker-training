<?php
// Start the session
require_once __DIR__ . '/helpers/Csrf.php';

session_start();
require_once 'models/UserModel.php';
$userModel = new UserModel();

$user = NULL; //Add new user
$_id = NULL;

if (!empty($_GET['id'])) {
    $_id = $_GET['id'];
    $user = $userModel->findUserById($_id); //Update existing user
}


if (!empty($_POST['submit'])) {
    if (!Csrf::verifyToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['message'] = "Invalid CSRF token, please try again.";
        header("Location: form_user.php");
        exit;
    }
    if (!empty($_id)) {
        $userModel->updateUser($_POST);
    } else {
        $userModel->insertUser($_POST);
    }
    header('Location: list_users.php');
    exit;
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>User form</title>
    <?php include 'views/meta.php' ?>
</head>

<body>
    <?php include 'views/header.php' ?>
    <div class="container">

        <?php if ($user || !isset($_id)) { ?>
            <div class="alert alert-warning" role="alert">
                User form
            </div>
            <form method="POST">
                <?php echo Csrf::inputField(); ?>
                <?php if (!empty($_SESSION['message'])): ?>
                    <div class="alert alert-danger">
                        <?php
                        echo htmlspecialchars($_SESSION['message']);
                        unset($_SESSION['message']);
                        ?>
                    </div>
                <?php endif; ?>
                <input type="hidden" name="id" value="<?php echo $_id ?>">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input class="form-control" name="name" placeholder="Name" value='<?php if (!empty($user[0]['name'])) echo $user[0]['name'] ?>'>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Password">
                </div>

                <button type="submit" name="submit" value="submit" class="btn btn-primary">Submit</button>
            </form>
        <?php } else { ?>
            <div class="alert alert-success" role="alert">
                User not found!
            </div>
        <?php } ?>
    </div>
</body>

</html>