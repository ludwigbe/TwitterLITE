<?php
# if no SESSION variable is passed, user gets send to LogIn
# User can't enter OwnProfile without LogIn

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['userId'])){
    header('Location: ../View/LogIn.php');
    exit();
}

include( '../Controller/db.php'); 
include( '../Controller/functions.php');


?>

<!DOCTYPE html>

<html>
<head>

    <title>
        TwitterLITE | My Profile
    </title>

    <link rel="stylesheet" type="text/css" href="..\View\Css\styles.css">
    <link rel="shortcut icon" type="image/png" href="Pictures\favicon.png">

</head>
<body>

<header>

    <h1 class="center" >
        My Profile
    </h1>

    <form action="../View/Startpage.php" method="POST">

        <!-- Startpage Button -->
        <div class="left">
            <button class="iconButton" type="submit" name="gettoStartpage"><img src='../View/Pictures/Icons/icon Startseite.PNG' alt='Startpage' width="30" height="30"></button>
        </div>

    </form>
    
    <form action="../View/Settings.php" method="POST">

        <!-- Settings Button --> 
        <div class="right">
            <button class="iconButton" type="submit" name="gettoEinstellungen"><img src='../View/Pictures/Icons/icons einstellungen.PNG' alt='Settings' width="30" height="30"></button>
        </div>

    </form>
    
</header>

<div class="content">

    <!-- User Profile Details -->
    <div class="userInformationContainer">

                

        <!-- Username -->
        <div class="usernameBig">
            
            <?php 
                echo getUsernameString($_SESSION['userId']) . "<br>";
            ?>

        </div>

        <!-- Follower -->
        <div class="follower">

            <div class="followerLabel">
                Follower
            </div>

            <div class="followerNumber">
                <?php
                    echo getFollowerNumber($_SESSION['userId']) . "<br>";  
                ?>
            </div>

        </div>

        <!-- Following -->
        <div class="following">

            <div class="followingLabel">
                Following
            </div>

            <div class="followingNumber">
                <?php
                    echo getFollowingNumber($_SESSION['userId']) . "<br>";  
                ?>
            </div>

        </div>
    </div>
            


   



    <!-- show all Posts from user, order by date --> 
    <div class="postcontainerUser">

        
        <?php
            # get all PostIds from current User
            $allPostIds = getPostIdsofUser($_SESSION['userId']);

            

            foreach($allPostIds as $postId){
                printPost($postId);
            }
            
            if (isset($_POST['likeunlike'])){
                likeButton($_POST['likeunlike'], $_POST['postId']);
            }   
        ?>

    </div>

</div>


</body>
</html>
