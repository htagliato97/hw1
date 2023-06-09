<?php
require_once "init.php";
if (isset($_SESSION["UID"])) {
    $escapedPassword = mysqli_escape_string($conn, $_SESSION['userid']);
    $escapedUser = mysqli_escape_string($conn, $_SESSION['username']);
    $query = "SELECT username from hw1_users where password = '{$escapedPassword}' and username = '{$escapedUser}'";
    $queryRes = mysqli_query($conn, $query);

    if ($queryRes) {
        if (mysqli_num_rows($queryRes)) {
            mysqli_close($conn);
            header("Location: {$uri}/../home.php");
            exit();
        }
    }
}

if (mysqli_errno($conn)) {
    echo "error";
    echo mysqli_connect_error();
    exit();
}

$uploadDir = 'upload';

$respCode = '';

if (isset($_GET['searchUser'])) check(null);

function check($username)
{
    global $conn;
    if (isset($username)) {
        $query = "SELECT username from hw1_users where username = '{$username}'";
        $queryRes = mysqli_query($conn, $query);

        if ($queryRes) {
            if (mysqli_num_rows($queryRes) > 0) {
                
                mysqli_free_result($queryRes);
                return true;

            }
            return false;
        }

    } else if (isset($_GET["searchUser"])) {
        $checkUser = mysqli_escape_string($conn, $_GET["searchUser"]);
        $query = "SELECT username from hw1_users where username = '{$checkUser}'";
        $queryRes = mysqli_query($conn, $query);
        if ($queryRes) {
            if (mysqli_num_rows($queryRes) > 0) {
                echo "found";
                mysqli_free_result($queryRes);
                mysqli_close($conn);
                exit();
            } else {
                echo "not found";
                mysqli_close($conn);
                exit();
            }
        }
    }
}

$nome = '';
$cognome = '';
$email = '';
$username = '';
$password = '';
$image_url = '';


if (isset($_POST["nome"])) {


    do {
        $nome = mysqli_real_escape_string($conn, $_POST["nome"]);
        $cognome = mysqli_real_escape_string($conn, $_POST["cognome"]);
        $email = mysqli_real_escape_string($conn, $_POST["email"]);
        $username = mysqli_real_escape_string($conn, $_POST["username"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $respCode .= ' Email Invalida';
            break;
        }
        if (check($username)) {
            $respCode .= ' Username found';
            break;
        }
        $conn = mys_con();
        $password = mysqli_real_escape_string($conn, $_POST["password"]);
        if (strlen($password) < 8) {
            $respCode .= " Lunghezza password invalida. Inserire almeno 8 caratteri";
            break;
        }
        $password = hash("sha256", $password);
        if (empty($nome) || empty($password) || empty($email) || empty($username)) {
            $respCode .= " Campi Invalidi";
            break;
        }

        if (!empty($_POST['file_upload']) && file_exists("./uploads/{$_POST['file_upload']}")) {
            $im_url = mysqli_escape_string($conn, $_POST['file_upload']);
            $image_url = "./uploads/{$im_url}";
        }
        else {
            $image_url = "./uploads/blank-profile-picture.png";
        }

        if (empty($image_url)) {
            $respCode .= ' Inserire immagine del profilo';
            break;
        }

        $query = "INSERT INTO hw1_users (nome, cognome, email, password, image, username) values ('{$nome}', '{$cognome}', '{$email}', '{$password}', '{$image_url}', '{$username}')";
        mysqli_query($conn, $query);
        $possibleID = mysqli_insert_id($conn);
        $successful = false;
        if (!mysqli_errno($conn)) $successful = true;
        if ($successful) {
            mysqli_close($conn);
            $_SESSION["userid"] = $password;
            $_SESSION['UID'] = $possibleID;
            $_SESSION['username'] = $username;
            $_SESSION['image'] = $image_url;
            header("Location: {$uri}/../home.php");
            exit();
        }
        mysqli_close($conn);
        header("Location: {$uri}/../signup.php");
        exit();
    } while (0);
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta content="Signup page" name="description">
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
    <title>Signup</title>
    <link rel="icon" href="favicon.png" sizes="64x64" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;400&display=swap" rel="stylesheet">
    <link href="general.css" rel="stylesheet" type="text/css">
    <link href="signup.css" rel="stylesheet" type="text/css">
    <script defer type="text/javascript">
        const uri = "<?php echo $uri ?>";
        let respCode = "<?php echo $respCode ?>";
    </script>
    <script defer type="text/javascript" src="signup.js"></script>
</head>
<body>
<div class="container">
    <h1 class="register">Registrati</h1>
    <a class="login" href="<?php echo "{$uri}/../login.php" ?>">Login</a>
    <form id="register" method="post" action="./signup.php">
        <div>
            <label for="nome">First name: </label>
            <input id="nome" class="post" type="text" name="nome" <?php if (!empty($nome)) { ?> value="<?php echo $nome ?>" <?php } ?>>
        </div>
        <div>
            <label for="cognome">Last name: </label>
            <input id="cognome" class="post" type="text" name="cognome" <?php if (!empty($cognome)) { ?> value="<?php echo $cognome ?>" <?php } ?>>
        </div>
        <div>
            <label for="email">Email: </label>
            <input id="email" class="post" type="email" name="email" <?php if (!empty($email)) { ?> value="<?php echo $email ?>" <?php } ?>>
            <span id="email-error" class="error" aria-live="polite"></span>
        </div>
        <div>
            <label for="username">Username: </label>
            <input id="username" class="post" name="username" type="text" <?php if (!empty($username)) { ?> value="<?php echo $username ?>" <?php } ?>>
            <span id="username-error" class="error" aria-live="polite"></span>
        </div>
        <div>
            <label for="password">Password: </label>
            <input id="password" name="password" type="password" class="post" required>
            <span id="password-error" class="error" aria-live="polite"></span>
        </div>
        <div>
            <label for="password_confirm">Confirm password:</label>
            <input id="password_confirm" name="password_confirm" class="post" type="password">
            <span id="password_confirm-error" class="error" aria-live="polite"></span>
        </div>
        <div id="image_chooser">
            <div>
                <label for="file_upload" id="label_image">Choose an image (optional)</label>
                <input type="file" id="file_upload" name="file_upload" accept="image/*">
            </div>
            <div class="prev">
                <p>You can select an image</p>
            </div>
        </div>

        <input class="button cool-submit-button" value="Invia" type="submit" name="form_submit">
    </form>
</div>
</body>
</html>
