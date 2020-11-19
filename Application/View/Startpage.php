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
    function postsOnStartpage($userId){

        $postIds=refreshPosts($userId);

        if(empty($postIds)){
            echo "<div class='noFriends' >Follow your Friends!</div>";
        }else{
            foreach($postIds as $postId){
                printPost($postId);
            }
        }
        
    }

?>



<!DOCTYPE html>
<html>
<head>

    <title>
        TwitterLITE | Startpage
    </title>
    
    <link rel="stylesheet" type="text/css" href="..\View\Css\styles.css">
    <link rel="shortcut icon" type="image/png" href="Pictures\favicon.png">


    
    <script type="text/javascript">

        // limits the row number of textarea
        function limitLines(obj, limit) {
            var values = obj.value.replace(/\r\n/g,"\n").split("\n")
            if (values.length > limit) {
                obj.value = values.slice(0, limit).join("\n")
            }
        }

    </script>


</head>

<body>

<header>

    <!-- Logo-->
    <div class="left">

        <img id="logoStartpage" src="../View/Pictures/Twitter_LITE transparent Schrift weiß.png" alt="Logo">
            
    </div>

    <!-- Heading -->
    <h1 class="center" >Startpage</h1>
    <!-- My Profile Button -->
    <div>
        <form class="right" action="../View/OwnProfile.php" method="POST">
            
            
            <button class="iconButton" type="submit" name="gettoOwnProfile"><img src='../View/Pictures/Icons/icons My Profile.PNG' alt='MyProfile' widht="70" height="70"></button>
            

        </form>
    </div>

</header>

<br>


<div class="content">


    <!-- Startpage Right Side -->
    <div class="right">

        <div id="startpageRightContent">
            <!-- Create a new Post -->
            <form action="../Controller/functions.php" method="POST">


                <!-- new Post Input-->
                <div >
                    <textarea  id="newPost" name="newPostText" rows="15" cols="55" maxlength=300 placeholder="What are you doing?" onkeydown="limitLines(this, 7)"></textarea>
                </div>

                <!-- new Post Button-->
                <div >
                    <button type="submit" name="newPostSubmit" >Post it!</button>
                </div>
            
            </form>



            <!-- Search Box -->
            <div >

                <form method="POST">

                    <!-- Search Input -->
                    <div>
                        <input id="searchtextInput" type="text" name="searchUser" placeholder="Search here..."></input>
                    </div>

                </form>

                <!-- Search List -->
                <div>

                    <?php
                        # Search Process
                        if(array_key_exists("searchUser", $_POST)){
                            $searchInput = $_POST["searchUser"];
                            $usernameResults=viewData($searchInput);       
                            
                            echo "<ul id='searchList'>";
                    
                                foreach($usernameResults as $user){
                                    
                                    # can't find himself
                                    if($user['userId'] == $_SESSION['userId']){

                                        continue;
                                    }else{

                                        echo "<li>";
                                            $url = '../View/UserProfile.php?id='.$user['userId'].'';
                                            $username = $user['username'];
                                            
                                            echo "<a class='usernameSearchlist' href=".$url.">$username</a>";
                                        echo "</li>";
                                    }
                                    
                                }

                            echo "</ul>";

                        }
                    
                    ?>

                </div>

            </div>
        </div>        
    </div>


    <!-- Startpage Left Side -->
    

    <!-- Posts on Screen -->
    <div class="postcontainer">

        <?php
            postsOnStartpage($_SESSION['userId']);
        ?>

        <?php
            # Refresh Posts Process
            if(array_key_exists("refreshPosts", $_POST)){
                $postIds = refreshPosts($_SESSION["userId"]);
                
                # Print Post
                foreach($postIds as $postId){
                    printPost($postId);
                }

            }  


            # Like Process
            if (isset($_POST['likeunlike'])){
                likeButton($_POST['likeunlike'], $_POST['postId']);
            }      
        ?>

    </div>

    

</body>
</html>
