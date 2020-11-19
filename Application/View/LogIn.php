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
    <title>
        TwitterLITE | Log In
    </title>

    <link rel="stylesheet" type="text/css" href="../View/Css/styles.css">
    <link rel="shortcut icon" type="image/png" href="Pictures\favicon.png">

</head>

<body>

    <div>


        <div>

            <!-- Logo-->
            <div >
                <img class="logo" src="../View/Pictures/Twitter_LITE transparent Schrift weiß.png" alt="Logo">
            
            </div>

            <br>
            <br>


        </div>

        <!-- LogIn Process-->
        <div class="signIn">

            
            <form action="../Controller/functions.php" method="POST" >
                

                <div class="textInput">

                    <!-- Email Input -->
                    <input class="loginInput" type="text" name="email" placeholder="E-Mail">
                
                </div>
                


                <div class="textInput">

                    <!-- Password Input -->
                    <input class="loginInput" type="password" name="password" placeholder="Password">
                    

                </div>


                <div>

                    
                    <!-- Log In Button -->
                    <button class="loginButton" type="submit" name="submitLogIn">Log In</button>


                </div>


            </form>


            <div class="loginBottom">
                
                <div class="noAccount">
                    <p>
                        No account yet?
                    </p>
                </div>

                <!-- Back To Sign Up Button-->
                <div class="signUpLink">

                    <form action="../View/Registration.php">
                        <button class="backtoSign" type="submit" name="enterSignUp">Sign Up</button>

                    </form>

                </div>



            </div>

        </div>


    </div>

</body>

</html>



<?php
    
?>
