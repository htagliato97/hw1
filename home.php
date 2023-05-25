<?php
require_once "init.php";
if (isset($_SESSION["UID"])) {
    $escapedPassword = mysqli_escape_string($conn, $_SESSION['userid']);
    $escapedUser = mysqli_escape_string($conn, $_SESSION['username']);
    $query = "SELECT username from hw1_users where password = '{$escapedPassword}' and username = '{$escapedUser}'";
    $queryRes = mysqli_query($conn, $query);

    if ($queryRes) {
        if (mysqli_num_rows($queryRes) == 0) {
            session_unset();
            session_destroy();
            mysqli_free_result($queryRes);
            mysqli_close($conn);
            header("Location: {$uri}/../login.php");
            exit();
        }
        mysqli_free_result($queryRes);
    } else {
        mysqli_close($conn);
        header("Location: {$uri}/../login.php");
        exit();
    }
} else {
    mysqli_close($conn);
    header("Location: {$uri}/../login.php");
    exit();
}

if (isset($_GET['postList'])) {
    $query = "SELECT playlistDeezerId, date, pl.ID as playlistId, username from hw1_playlists pl join hw1_users on pl.playlistUser = hw1_users.ID
           WHERE playlistUser in (select followed from hw1_follow join hw1_users on followerId = ID where username = '{$_SESSION['username']}') ORDER BY date DESC"; // AGGIUNGERE LA CONDIZIONE OR PER GLI ALTRI UTENTI

    $queryRes = mysqli_query($conn, $query);
    $arrayOfInfo = array();

    if (!$queryRes) {
        mysqli_close($conn);
        http_response_code(404);
        exit();
    }

    while ($row = mysqli_fetch_assoc($queryRes)) {
        $query = "SELECT playlistId from hw1_playlistlike join hw1_users on userId = ID where username = '{$_SESSION['username']}' and playlistId = {$row['playlistId']}";

        $likeRes = mysqli_query($conn, $query);
        $query = "SELECT * from hw1_playlistlike where playlistId = {$row['playlistId']}";
        $numLikesRes = mysqli_query($conn, $query);
        $numLikes = mysqli_num_rows($numLikesRes);


        array_push($arrayOfInfo, array('playlistId' => $row['playlistId'], 'date' => $row['date'], 'like' => $likeRes && mysqli_num_rows($likeRes) > 0, 'num_likes' => $numLikes, 'playlist_deezer_id' => $row['playlistDeezerId'], 'username' => $row['username']));
        mysqli_free_result($likeRes);
    }
    mysqli_free_result($queryRes);
    mysqli_close($conn);
    echo json_encode($arrayOfInfo);
    exit();
}

if (isset($_GET['resolveLike']) && !empty($_GET['resolveLike'])) {
    $escapedPlaylistId = mysqli_escape_string($conn, $_GET['resolveLike']);


    $query = "SELECT playlistId FROM hw1_playlistlike JOIN hw1_users ON userId = ID WHERE username = '{$_SESSION['username']}' AND playlistId = '{$escapedPlaylistId}'";

    $queryRes = mysqli_query($conn, $query);

    if ($queryRes) {
        if (mysqli_num_rows($queryRes) > 0) {
            mysqli_free_result($queryRes);
            $query = "DELETE FROM hw1_playlistlike WHERE playlistId = '{$escapedPlaylistId}' AND userId IN (SELECT ID FROM hw1_users WHERE username = '{$_SESSION['username']}')";
            $queryRes = mysqli_query($conn, $query);

            echo 'Like';
        } else {
            mysqli_free_result($queryRes);
            $query = "INSERT INTO hw1_playlistlike (userId, playlistId) VALUES ({$_SESSION['UID']}, '{$escapedPlaylistId}')";
            $queryRes = mysqli_query($conn, $query);

            echo 'Unlike';
        }
    } else {
        echo 'Like';
    }
    mysqli_close($conn);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="icon" href="favicon.png" sizes="64x64" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="common.css">
    <link rel="stylesheet" href="home.css">
    <script defer src="home.js"></script>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            tuneshare | <span class="user-nav"><?php echo $_SESSION['username'] ?></span>
        </div>
        <!-- Add the hamburger menu icon -->
        <div class="hamburger-menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>

        <!-- Add the navigation drawer container -->
        <div id="navigation-drawer" class="navigation-drawer">
            <ul class="drawer-menus">
                <li><a href="<?php echo "{$uri}/../home.php" ?>">Home</a></li>
                <li><a href="<?php echo "{$uri}/../search_content.php" ?>">Search</a></li>
                <li><a href="<?php echo "{$uri}/../search_people.php" ?>">Contacts</a></li>
                <li><a href="<?php echo "{$uri}/../my_library.php" ?>">My Library</a></li>
                <li><a href="<?php echo "{$uri}/../logout.php" ?>">Logout</a></li>
            </ul>
        </div>

        <!-- Add the backdrop container -->
        <div id="backdrop" class="backdrop"></div>

        <ul class="menus">
            <li><a href="<?php echo "{$uri}/../home.php" ?>">Home</a></li>
            <li><a href="<?php echo "{$uri}/../search_content.php" ?>">Search</a></li>
            <li><a href="<?php echo "{$uri}/../search_people.php" ?>">Contacts</a></li>
            <li><a href="<?php echo "{$uri}/../my_library.php" ?>">My Library</a></li>
            <li><a href="<?php echo "{$uri}/../logout.php" ?>">Logout</a></li>
        </ul>
    </nav>

    <div class="grid">
    </div>

</body>

</html>