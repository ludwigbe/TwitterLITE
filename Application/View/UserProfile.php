<?php


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['userId'])){
    header('Location: ../View/LogIn.php');
    exit();
}


include( '../Controller/db.php'); 
include( '../Controller/functions.php');

$userId = mysqli_real_escape_string($conn, $_GET['id']);


# Functions
function followButton($value){
    global $userId;        

    if($value=="follow"){
            
        // Database entry            
        followUser($_SESSION['userId'], $userId);
        
        ?>
        <script>

            // Button Change
            document.getElementById("0").value = "unfollow";
            document.getElementById("0").style.backgroundColor = "#757109";
            document.getElementById("0").innerHTML = "Following";
            
        
        </script>
        
        <?php

    }

    if($value=="unfollow"){

        // Database entry
        unfollowUser($_SESSION['userId'], $userId);

        ?>
        <script>

            // Button Change
            document.getElementById("0").value = "follow";
            document.getElementById("0").style.backgroundColor = "#D8B861";
            document.getElementById("0").innerHTML = "+Follow";
        </script>
        
        <?php            

    }


}


?>

<!DOCTYPE html>
<html>
<head>
    <title>
        TwitterLITE | UserProfile
    </title>

    <link rel="stylesheet" type="text/css" href="..\View\Css\styles.css">
    <link rel="shortcut icon" type="image/png" href="Pictures\favicon.png">
    
</head>
<body>

<header>

    <form action="../View/Startpage.php" method="POST">

        <!-- Back to Startpage Button -->
        <div class="left">
            <button type="submit" name="gettoStartpage">Back</button>
        </div>

    </form>
        
    <h1 class="center">
        
        <?php
            
        ?>
        
    </h1>

</header>

<div class="content">
    
    <!-- User Profile Details -->
    <div class="userInformationContainer">
            

        <!-- Username -->
        <div class="usernameBig">
            
            <?php 
                echo getUsernameString($userId) . "<br>";
            ?>

        </div>

        <!-- Follower -->
        <div class="follower">

            <div class="followerLabel">
                Follower
            </div>

            <div class="followerNumber">
                <?php
                    echo getFollowerNumber($userId) . "<br>";  
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

                    echo getFollowingNumber(intval($userId)) . "<br>";  
                ?>
            </div>

        </div>

        
    </div>

    <!-- Follow Button -->
    <div>
        <form method="POST">
            
            <?php
            
                if(isUserfollowing($_SESSION['userId'], $userId)){
                    
                    ?>
                        <!-- Unfollow Button -->
                        <div>
                            <button class="followingButton" type="submit" id="0" name="followUnfollow" value="unfollow">Following</button>
                        </div>
    
                    <?php
                }else{
                    
                    ?>

                        <!-- Follow Button -->
                        <div>
                            <button class="followButton" type="submit" id="0" name="followUnfollow" value="follow">+Follow</button>
                        </div>
                        
                    <?php
                }

            ?>
        

        </form>


    </div>


    

    <?php

    if (isset($_POST['followUnfollow'])){
        followButton($_POST['followUnfollow']);
    }

    ?>



    <!-- show all Posts from user, order by date --> 
    <div class="postcontainerUser">
        <?php

            # get all PostIds from current User
            $allPostIds = getPostIdsofUser($userId);

            foreach($allPostIds as $postId){
                printPost($postId);
            }
            
        ?>
    </div>


</div>



</body>

</html>



