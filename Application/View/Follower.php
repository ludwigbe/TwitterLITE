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

?>


<!DOCTYPE html>
<html>
<head>
    <title>
        TwitterLITE | Follower
    </title>

    <link rel="stylesheet" type="text/css" href="..\View\Css\styles.css">
    <link rel="shortcut icon" type="image/png" href="Pictures\favicon.png">
    
</head>

<body>

    <header>

        <h1 class="center">Follower</h1>

    </header>

    <br>
    <br>

    <div class="content">


        <?php

            $userArray = getUserAIds($userId);
            $userArray = userIdArrayOrderByName($userArray);
            
                        
            if(empty($userArray)){
                ?>
                    <div class="noContent">
                        <?php
                        echo "<div class='noFollower'>Follow your Friends!</div>";
                        ?>
                    </div>
                <?php

            }else{?>

                <!-- Follower List -->
                <div class="followList">
    
                    <ul>
                        <?php
                            foreach($userArray as $user){
                                ?>
                                <li>
                                    <?php
                                    echo getUsername($user) . "<br>";
                                    ?>
                                </li>
                                <?php
                            }   
                        
                        ?>
                    </ul>
    
                </div>
                
                <?php               
            }


        ?>
    </div>

</body>
</html>
