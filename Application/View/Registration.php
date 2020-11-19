<?php

    # Functions Connection
    include( '../Controller/functions.php');

    if (isset($_SESSION['userId'])){
        header('Location: ../View/Startpage.php');
        exit();
    }

?>

<!DOCTYPE html>
<html>
<head>
    
    <title>TwitterLITE | Sign Up</title>

    <link rel="stylesheet" type="text/css" href="..\View\Css\styles.css">
    <link rel="shortcut icon" type="image/png" href="Pictures\favicon.png">

</head>
<body>


    <div>

        <div >
            <img class="logo" src="../View/Pictures/Twitter_LITE transparent Schrift weiß.png" alt="Logo">
        
        </div>

        <br>
        <br>
        
    </div>

    <!-- Sign Up Process-->   
    <div class="signIn">

        <form action="../Controller/functions.php" method="POST" >
        
            <!-- Email Input -->
            <div class="textInput">
                <input class="loginInput" type="text" name="email" placeholder="E-Mail adress">
                <br>
            </div>

            <!-- Username Input -->
            <div class="textInput">
                <input class="loginInput" type="text" name="username" placeholder="Username">
                <br>
            </div>

            <!-- Password Input -->
            <div class="textInput">
                <input class="loginInput" type="password" name="password" placeholder="Password">
                <br>
            </div>

            <!-- Password Repeat Input -->
            <div class="textInput">
                <input class="loginInput" type="password" name="repeatpassword" placeholder="Repeat Password">
                <br>
            </div>


            <!-- Sign Up Button-->
            <div>

                <button class="loginButton" type="submit" name="submitRegistry">Sign Up</button>

            </div>

        </form>


        <div class="loginBottom">

            <div class="alreadyAccount">
                <p>
                    Already have an acoount?
                </p>
            </div>
                



            <!-- Back To Log In Button-->
            <div class="signinLink">

                <form action="../View/LogIn.php">
                    <button class="backtoSign" type="submit" name="enterSignIn">Back to Log In</button>

                </form>

            </div>

        </div>


    </div>

</body>
</html>