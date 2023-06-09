<?php
require_once "init.php";
if (isset($_SESSION["UID"])) {
    $escapedPassword = mysqli_escape_string($conn, $_SESSION['userid']);
    $escapedUser = mysqli_escape_string($conn, $_SESSION['username']);
    $query = "SELECT username from hw1_users where password = '{$escapedPassword}' and username = '{$escapedUser}'";

    $queryRes = mysqli_query($conn, $query);
    if ($queryRes) {
        if (mysqli_num_rows($queryRes)) {
            mysqli_free_result($queryRes);
            mysqli_close($conn);
            header("Location: {$uri}/../home.php");
            exit();
        }
        mysqli_free_result($queryRes);
    }
    mysqli_close($conn);
}


function checkUserInfo($username, $password)
{
    global $conn, $uri;
    $conn = mys_con();
    $query = "SELECT username, ID, image from hw1_users where username = '{$username}' and password = '{$password}'";
    $queryRes = mysqli_query($conn, $query);
    if ($queryRes) {
        if (mysqli_num_rows($queryRes) == 1) {
            $row = mysqli_fetch_assoc($queryRes);
            $user = $row['username'];
            $UID = $row['ID'];
            $image = $row['image'];
            mysqli_free_result($queryRes);
            mysqli_close($conn);
            $_SESSION["userid"] = $password;
            $_SESSION["username"] = $user;
            $_SESSION['UID'] = $UID;
            $_SESSION['image'] = $image;
            header("Location: {$uri}/../home.php");
            exit();
        }
    }
    mysqli_close($conn);
}

if (isset($_POST["username"])) {
    $username = mysqli_escape_string($conn, trim($_POST["username"]));
    $password = hash("sha256", mysqli_escape_string($conn, $_POST["password"]));
    checkUserInfo($username, $password);

}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta content="login page" name="description">
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
    <title>Login</title>
    <link rel="icon" href="favicon.png" sizes="64x64" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;400&display=swap" rel="stylesheet">
    <link href="general.css" rel="stylesheet" type="text/css">
    <link href="login.css" rel="stylesheet" type="text/css">
    <script async defer src="login.js"></script>
</head>
<body>
<div class="container">
    <h1 class="login">Login</h1>
    <a class="signup" href="<?php echo "{$uri}/../signup.php" ?>">Signup</a>
    <form id="login" method="post" action="./login.php" enctype="multipart/form-data">
        <div>
            <label for="username">Username: </label>
            <input id="username" class="post" name="username" type="text">
        </div>
        <div>
            <label for="password">Password: </label>
            <input id="password" name="password" type="password" class="post">
        </div>

        <div>
            <label for="remember">Remember: </label>
            <input type="checkbox" id="remember" name="remember">
        </div>

        <input class="button cool-submit-button" value="Invia" type="submit" name="form_submit">
    </form>
</div>
</body>
</html>

