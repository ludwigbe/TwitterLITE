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


?>

<!DOCTYPE html>
<html>
<head>
    
    <title>
        TwitterLITE | Settings
    </title>
    
    <link rel="stylesheet" type="text/css" href="..\View\Css\styles.css">
    <link rel="shortcut icon" type="image/png" href="Pictures\favicon.png">

</head>

<body>
    <header>

        <h1 class="center" >
            Settings
        </h1>
        
        <form action="../View/OwnProfile.php" method="POST">

            <!-- Back to MyProfile Button --> 
            <div class="left">
                <button type="submit" name="backtoMyProfile">Back</button>
            </div>

        </form>



    </header>

    <div class="content">
        <div class="centerMargin">
            <form action="../View/EditProfile.php" >

                <!-- Edit Profile Button -->
                <div >
                    <button>Edit Profile</button>
                </div>

            </form>

            
            <form method="POST">

                <!-- Log Out Button --> 
                <div>
                    <button id="logoutButton" name="logoutButton">Log Out</button>
                </div>

            </form>
        </div>
    </div>

    <?php

        if (isset($_POST['logoutButton'])){
            logout();
        }

    ?>
</body>
</html>