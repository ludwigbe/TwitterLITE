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


    # Functions
    function saveSettings($usernameInput){

        if(!empty($usernameInput)){
            $usernameInput = replaceSpace($usernameInput);
            
            setUsername($_SESSION['userId'], $usernameInput);
            header('Location: ../View/OwnProfile.php?EditProfile=Success');
        }

        if(empty($usernameInput)){
            echo "<div class='noUsername'>Please enter a new username.</div>";
        }


    }
?>

<!DOCTYPE html>
<html>
<head>
    
    <title>
        TwitterLITE | Edit Your Profile
    </title>

    <link rel="stylesheet" type="text/css" href="..\View\Css\styles.css">
    <link rel="shortcut icon" type="image/png" href="Pictures\favicon.png">
    
</head>

<body>


    <header>

        <h1 class="center" >
            Edit Profile
        </h1>
        
        <form action="../View/Settings.php" method="POST">

            <!-- Back to Settings--> 
            <div class="left">
                <button type="submit">Back</button>
            </div>

        </form>

    </header>
   

    <div class="content">

        <div class="centerMargin">
            <form method="POST">

                <!-- new Username Input -->
                <div class="textInput">
                    <input type="text" name="usernameInput" placeholder="new Username">
                </div>
                
                <!-- Save Button -->
                <div>
                    <button class="submitEditProfile" type="submit" name=saveEditProfile>Save</button>
                </div>

            </form>
        </div>
    </div>


    <?php
    
        if (isset($_POST['saveEditProfile'])){
            saveSettings($_POST['usernameInput']);
        }

    ?>

</body>
</html>