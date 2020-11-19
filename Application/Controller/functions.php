<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


?>

<head>
    
    <link rel="stylesheet" type="text/css" href="..\View\Css\styles.css">
</head>

<?php

# Database Connection
include('../Controller/db.php');

# SESSION Variables

# catches POST DATA 
if (isset($_POST['submitRegistry']) || isset($_POST['submitLogIn']) || isset($_POST['refreshPosts']) || isset($_POST['newPostSubmit']) || isset($_POST['newCommentSubmit']) || isset($_POST['searchUser']) || isset($_GET['deletePost']) || isset($_GET['deleteComment'])) {

    # Register Process with SQL Injection 
    if (array_key_exists("submitRegistry", $_POST)) {
        $emailInput = mysqli_real_escape_string($conn, $_POST["email"]);
        $usernameInput = mysqli_real_escape_string($conn, $_POST["username"]);
        $passwordInput = mysqli_real_escape_string($conn, $_POST["password"]);
        $repeatpasswordInput = mysqli_real_escape_string($conn, $_POST["repeatpassword"]);

        registerUser($emailInput, $usernameInput, $passwordInput, $repeatpasswordInput);
    }

    # LogIn Process with SQL Injection 
    if (array_key_exists("submitLogIn", $_POST)) {
        $emailInput = mysqli_real_escape_string($conn, $_POST["email"]);
        $passwordInput = mysqli_real_escape_string($conn, $_POST["password"]);

        loginUser($emailInput, $passwordInput);

    }

    # New Post Process with SQL Injection 
    if (array_key_exists("newPostSubmit", $_POST)) {
        $postInput = mysqli_real_escape_string($conn, $_POST["newPostText"]);

        # checks if Input is empty or only Spacebar
        if (strlen(trim($postInput)) == 0){
            header('Location: ../View/Startpage.php?error=emptyPost');
            exit();
        }

        createNewPost($postInput);
    }

    # New Comment Process with SQL Injection 
    if (array_key_exists("newCommentSubmit", $_POST)) {

        $commentInput = mysqli_real_escape_string($conn, $_POST["newCommentText"]);
        $postId = $_POST['postId'];
        $url = $_POST['url'];
        $user = $_POST['user'];

        # checks if Input is empty or only Spacebar
        if (strlen(trim($commentInput)) == 0)  {

            # if View was foreign User Profile, he gets back with '$user' as userId
            header('Location: '.$url.'?error=emptyComment&id='.$user);
            exit();
        }

        createNewComment($postId, $_SESSION['userId'], $commentInput);
    }

    # Delete Post Process 
    if (isset($_GET['deletePost'], $_POST)) {
        deletePost($_GET['deletePost']);
        header('Location: ../View/OwnProfile.php');
        exit();
    }

    # Delete Comment Process
    if (isset($_GET['deleteComment'], $_GET['url'], $_GET['id'], $_POST)) {
        deleteComment($_GET['deleteComment']);
        $url = $_GET['url'];
        $id = $_GET['id'];

        # if View was foreign User Profile, he gets back with '$id' as userId
        header('Location: '.$url.'?id='.$id);
        exit();
    }

}

# search Process with SQL Injection 
function viewData($username){
    global $conn;

    $data = array();

    $username = mysqli_real_escape_string($conn, $username);

    # search for Usernames
    $sql = "SELECT username, userId FROM user WHERE username LIKE '%" . $username . "%';";
    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($result)) {

        $data[] = $row;

    }

    return $data;
}


# Register Process with SQL Injection
function registerUser($emailInput, $usernameInput, $passwordInput, $repeatpasswordInput){
    global $conn;


    # Checks, if the User let an Input Field empty and sends User back to Login
    if (empty($emailInput) || empty($usernameInput) || empty($passwordInput) || empty($repeatpasswordInput)) {
        header('Location: ../View/Registration.php?error=emptyfields');
        exit();


    } else {

        $username = $usernameInput;

        $email = checkEmail($emailInput);

        $password = checkPassword($passwordInput, $repeatpasswordInput);

        $email = mysqli_real_escape_string($conn, $email);
        $username = mysqli_real_escape_string($conn, $username);
        $password = mysqli_real_escape_string($conn, $password);

        $username = replaceSpace($username);

        $sql = "INSERT INTO `user`(`email`, `username`, `password`) VALUES ('" . $email . "', '" . $username . "', '" . $password . "');";
        mysqli_query($conn, $sql);

        header('Location: ../View/LogIn.php?SignUp=Success');
        exit();
    }


}

# Register Process: Checks Email
function checkEmail($email){
    global $conn;

    # Checks the Email Form
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        header('Location: ../View/Registration.php?error=unvalidemail');
        exit();

    } else {

        $email = mysqli_real_escape_string($conn, $email);

        # Checks the Database if the Email aready exists
        $sql = "SELECT * from user WHERE email = '" . $email . "';";
        $table = mysqli_query($conn, $sql);

        if (mysqli_num_rows($table) < 1) {

            return $email;
        } else {

            header('Location: ../View/Registration.php?error=emailgiven');
            exit();
        }
    }
}

# Checks, if password and the repeated password matches
# Returns the hashed Password or exits
function checkPassword($password, $repeatpassword){
    if ($password != $repeatpassword) {

        header('Location: ../View/Registration.php?error=passworderror');
        exit();
    }

    $hashedpassword = password_hash($password, PASSWORD_DEFAULT);

    return $hashedpassword;
}


# LogIn Process
function loginUser($emailInput, $passwordInput){
    global $conn;


    # Checks, if the User let an Input Field empty and sends User back
    if (empty($emailInput) || empty($passwordInput)) {
        header('Location: ../View/LogIn.php?error=emptyfields');
        exit();
    } else {


        # Checks the Email and Password and sends User to Starpage
        checkDatabaseEmail($emailInput);
        checkDatabasePassword($emailInput, $passwordInput);

    }


}

function checkDatabaseEmail($emailInput){
    global $conn;

    $emailInput = mysqli_real_escape_string($conn, $emailInput);

    # Checks, if the email does exist in Database
    $sql = "SELECT * from user WHERE email = '" . $emailInput . "';";
    $table = mysqli_query($conn, $sql);

    if (mysqli_num_rows($table) < 1) {

        # Email does not exist
        header('Location: ../View/LogIn.php?error=emaildoesnotexist');
        exit();
    }


    $sql = "SELECT userId FROM user where email = '" . $emailInput . "'";
    $result = mysqli_query($conn, $sql);

    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

    $_SESSION["userId"] = $row['userId'];

}

# Checks the Password 
function checkDatabasePassword($emailInput, $passwordInput){
    global $conn;

    $emailInput = mysqli_real_escape_string($conn,  $emailInput);

    # Checks, if the password is correct
    $sql = "SELECT password FROM user where email = '" . $emailInput . "'";
    $table = mysqli_query($conn, $sql);

    $row = mysqli_fetch_array($table, MYSQLI_ASSOC);
    $passwordFromDatabase = $row['password'];


    # Compares the password and sends User to Startpage
    if (password_verify($passwordInput, $passwordFromDatabase)) {
        
        header('Location: ../View/Startpage.php?LogIn=Success');
        exit();
    } else {
        
        # password is not correct and sends User back to LogIn
        session_destroy();
        header('Location: ../View/LogIn.php?password=incorrect');
        exit();
    }

}


# User Functions

# returns the Username as a Html link to UserProfile
function getUsername($userId){

    global $conn;

    # get username with SQL Query
    $sql = "SELECT `username` FROM `user` WHERE `userId` = $userId ";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $username = $row['username'];

    if ($userId == $_SESSION['userId']) {


        return "<a class='username' href='../View/OwnProfile.php'>$username</a>";
    } else {

        return "<a class='username' href='../View/UserProfile.php?id=$userId'>$username</a>";
    }

}

# returns the Username as a String
function getUsernameString($userId){
    global $conn;


    # get username with SQL Query
    $sql = "SELECT `username` FROM `user` WHERE `userId` = $userId ";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $username = $row['username'];
    
    return $username;
}

function setUsername($usernameId, $input){
    global $conn;

    $input = mysqli_real_escape_string($conn, $input);

    $sql = "UPDATE `user` SET username='$input' WHERE userId='$usernameId';";
    $result = mysqli_query($conn, $sql);
}

# get Number of Follower for User with UserID and returns the number as a Html Link referencing the list of users
function getFollowerNumber($userId){
    $sqlFollower = "SELECT COUNT(userB) as follower FROM `user_follows` WHERE userB = $userId";
    $resultFollower = mysqli_query($GLOBALS["conn"], $sqlFollower);
    $rowFollower = mysqli_fetch_assoc($resultFollower);
    $number = $rowFollower['follower'];

    return "<a class='link' href='../View/Follower.php?id=$userId'  target='_blank'>$number</a>";

}

# get Number of Following Users for User with UserID and returns the number as a Html Link referencing the list of users
function getFollowingNumber($userId)
{
    $sqlFollowing = "SELECT COUNT(userA) as following FROM `user_follows` WHERE userA = $userId";
    $resultFollowing = mysqli_query($GLOBALS["conn"], $sqlFollowing);
    $rowFollowing = mysqli_fetch_assoc($resultFollowing);
    $number = $rowFollowing['following'];

    return "<a class='link' href='../View/Following.php?id=$userId' target='_blank'>$number</a>";

}

# get Number of Post of User with UserID
function getNumPost($userId){
    $sqlPost = "SELECT COUNT('userId') as number FROM `post` WHERE `userId` = '$userId' AND `deleted` = 0";
    $resultPost = mysqli_query($GLOBALS["conn"], $sqlPost);
    $rowPost = mysqli_fetch_assoc($resultPost);
    return $rowPost['number'];
}

# get all Posts of a User with UserID order by creation time Desc (oldest as first)
# returns PostIds as array 
function getPostIdsofUser($userId){
    global $conn;

    $postIdArray = array();

    $sql = "SELECT postId from post WHERE userId = $userId AND deleted = 0 ORDER BY creationTime DESC;";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {

            $postIdArray[] = $row['postId'];

        }
    } else {

        # if User got no Posts
        echo "<div class='noPosts'> <p>No Posts</p> </div>";
        exit();
        
    }

    return $postIdArray;
}

# stores in database: userA follows userB
function followUser($userA, $userB){
    global $conn;

    $sql = "INSERT INTO `user_follows`(`userA`, `userB`) VALUES ($userA, $userB);";
    $result = mysqli_query($conn, $sql);

    return $result;
}

# deletes in database: userA follows userB
function unfollowUser($userA, $userB){
    global $conn;

    $sql = "DELETE FROM `user_follows` WHERE userA=$userA AND userB=$userB";
    $result = mysqli_query($conn, $sql);

    return $result;
}

# checks if userA is following userB
function isUserfollowing($userA, $userB)
{
    global $conn;

    $sql = "SELECT * FROM `user_follows` WHERE userA=$userA AND userB=$userB";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 0) {

        # userA is not following
        return false;
    } else {

        # userA is following
        return true;
    }
}

# List of userId gets ordered by username ASC
function userIdArrayOrderByName($userArray){
    global $conn;

    $sql = "SELECT * FROM `user` WHERE ";
    $userIds = array();
    $length = sizeof($userArray);
    $counter = 0;

    foreach ($userArray as $user) {
        $counter++;
        $sql .= "userId='" . $user . "' ";
        if ($counter != $length) {
            $sql .= "OR ";
        } else {
            $sql .= "ORDER BY username;";

        }
    }

    $result = mysqli_query($conn, $sql);

    if ($result == false) {
        return $userIds;
    } else {
        while ($row = mysqli_fetch_assoc($result)) {

            $userIds[] = $row['userId'];

        }

        return $userIds;
    }

}

# get userIdArray from User he is following
function getUserBIds($userId){
    global $conn;

    $sql = "SELECT userB from user_follows WHERE userA = '" . $userId . "';";
    $result = mysqli_query($conn, $sql);

    $userBIdarray = array();


    # fill Array with UserB Id's
    while ($row = mysqli_fetch_assoc($result)) {

        $userBIdarray[] = $row['userB'];

    }

    return $userBIdarray;
}

# get userIdArray from User he gets followed
function getUserAIds($userId)
{
    global $conn;

    $sql = "SELECT userA from user_follows WHERE userB = '" . $userId . "';";
    $result = mysqli_query($conn, $sql);

    $userAIdarray = array();


    # fill Array with UserA Id's
    while ($row = mysqli_fetch_assoc($result)) {

        $userAIdarray[] = $row['userA'];

    }

    return $userAIdarray;
}

# gets List of all not deleted postIds from all users ordered by creationTime DESC
function getPostIdArray($userIdarray)
{
    global $conn;
    $postIds = array();

    $sql = "SELECT post.postId FROM user JOIN post ON user.userId=post.userId WHERE ";

    $length = sizeof($userIdarray);
    $counter = 0;

    foreach ($userIdarray as $user) {
        $counter++;
        $sql .= "user.userId='" . $user . "' ";
        if ($counter != $length) {
            $sql .= "OR ";
        } else {
            $sql .= "AND post.deleted=0 ORDER BY post.creationTime DESC;";

        }
    }

    $result = mysqli_query($conn, $sql);

    if ($result == false) {
        return $postIds;
    } else {
        while ($row = mysqli_fetch_assoc($result)) {

            $postIds[] = $row['postId'];

        }

        return $postIds;
    }

}


# Post Functions


# refresh Posts Process
function refreshPosts($userId){

    # Array of userIds the user is following
    $userBIds = getUserBIds($userId);

    # Array of postIds 
    $postIds = getPostIdArray($userBIds);

    return $postIds;

}

# defines the way a post gets displayed
function printPost($postId){
    global $conn;

    $userId = getUserIdPost($postId);
    $postTime = getCreationTimePost($postId);

    ?>

    <div class="post">
        <div>

            <div class="username">
                <?php


                #Username
                echo getUsername($userId) . "<br>";
                ?>
            </div>


            <div class="time">
                <?php

                #Time
                echo getTimeDif($postTime) . "<br>";


                ?>
            </div>

        </div>

        <div class="contentPost">
            <?php

            # Content
            $content =  getContentPost($postId);
            echo  replaceLinebreaks($content). "<br>";
            ?>
        </div>


        <div class="likeBar">

            <!-- number of Likes -->
            <div class="number">
                <?php
                echo getLikeNumberPost($postId);
                ?>
            </div>


            <?php

            # Like Button
            if (hasliked($_SESSION['userId'], $postId)) {
                ?>

                <form method="POST">
                    <input type="hidden" name="postId" value="<?php echo $postId; ?>">
                    <button class="likeButton" id="<?php echo $postId; ?>" name="likeunlike" value="like"><img src="..\View\Pictures\Icons\herzDisabled transparent.PNG" alt="Unliked" width="25" height="25"></button>
                </form>

                <?php
            } else {
                ?>

                <form method="POST">
                    <input type="hidden" name="postId" value="<?php echo $postId; ?>">
                    <button class="likeButton" id="<?php echo $postId; ?>" name="likeunlike" value="unlike"><img src="..\View\Pictures\Icons\herzEnabled transparent.PNG" alt="Liked" width="25" height="25"></button>
                </form>

                <?php
            } ?>
            <?php

            if (isset($_POST['likeunlike'])) {
                likeButton($_POST['likeunlike'], $_POST['postId']);
            }

            ?>


            <!-- number of Comments -->
            <div class="number">
                <?php
                echo getCommentNumberPost($postId);
                ?>
            </div>


            
            <img class="commentImage" src="../View/Pictures/Icons/icon Comment.PNG" alt="Comment" width="27" height="24" >
            


            <?php

            # Button with Html Link displayed like a Button: Delete Post
            if (getUserIdPost($postId) == $_SESSION['userId']) {


                ?>
                <div class="dropdown">
                    <button class="dropbtn"><img src='../View/Pictures/Icons/icons Options.PNG' alt='Options' width="55" height="55"></button>
                    
                    <div class="dropdown-content">

                        <a href='../Controller/functions.php?deletePost=<?php echo $postId; ?>'>Delete Post</a>

                    </div>
                    
                </div>
                <?php

            }
            ?>
        </div>

        <?php

        showComments($postId);


        ?>

    </div>
    <br>
    <?php

    
    
}

# new Post Process with SQL Injection
function createNewPost($content){
    global $conn;

    $content = mysqli_real_escape_string($conn, $content);

    $sql = "INSERT INTO `post`(`userId`, `content`) VALUES ('" . $_SESSION["userId"] . "','" . $content . "');";
    mysqli_query($conn, $sql);
    header("Location: ../View/Startpage.php?Upload=Success");
    exit();
}

# checks if user has liked this post
function hasliked($userId, $postId){
    global $conn;

    $sql = "SELECT * FROM `user_like_post` WHERE userId=$userId AND postId=$postId;";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result)) {

        # user not liked
        return false;
    } else {

        # user liked
        return true;
    }
}

# like Post with UserId and postID
function likePost($userId, $postId){
    global $conn;


    $sql = "INSERT INTO `user_like_post`(`userId`, `postId`) VALUES ($userId, $postId);";
    $result = mysqli_query($conn, $sql);

    return $result;

}

# unlike Post with UserId and postID
function unLikePost($userId, $postId){
    global $conn;


    $sql = "DELETE FROM `user_like_post` WHERE userId=$userId AND postId=$postId";
    $result = mysqli_query($conn, $sql);

    return $result;
}

function getContentPost($postId){
    global $conn;

    $sql = "SELECT content from post WHERE postId = '" . $postId . "'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    return $row['content'];
}


# get Number of Likes for Post with postID
function getLikeNumberPost($postId)
{
    global $conn;

    $sql = "SELECT likes from post WHERE postId = $postId";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['likes'];
}

#delete Post with PostId -> update on deleted
function deletePost($postId){
    global $conn;

    $sql = "UPDATE `post` SET `deleted`= 1 WHERE `postId`= '$postId'";
    mysqli_query($conn, $sql);
}

# gets creationTime of Post
function getCreationTimePost($postId){
    global $conn;

    $sql = "SELECT * FROM post WHERE postId=$postId;";
    $result = mysqli_query($conn, $sql);

    $row = mysqli_fetch_assoc($result);
    return $row['creationTime'];
}

# returns the userId from the user the post is from
function getUserIdPost($postId){
    global $conn;

    $sql = "SELECT * FROM post WHERE postId=$postId;";
    $result = mysqli_query($conn, $sql);

    $row = mysqli_fetch_assoc($result);
    return $row['userId'];
}


# Comment Functions

# defines the way a post gets displayed
function printComment($commentId){
    ?>
    <div class="commentContainer">   
        <?php

        $postId = getPostIdofComment($commentId);
        $user = getUserIdPost($postId);


        echo "<br>";
        ?>
        <div class="username">
            <?php
        
            # Username
            $userId = getUserIdComment($commentId);
            echo getUsername($userId) . "<br>";

            ?> 
        </div> 
                
        <div class="time">
            <?php

            # Time
            $time = getCreationTimeComment($commentId);
            echo getTimeDif($time) . "<br>";

            ?> 
        </div> 
        
        <div class="contentComment">
            <?php
            # Content
            echo getContentComment($commentId) . "<br>";
            ?>
        </div>
        
        <div class="likeBarComment">
            <?php

            # Button with Html Link displayed like a Button: Delete Post
            if (getUserIdComment($commentId) == $_SESSION['userId']) {

                ?>
                <div>
                    <div class="dropdownComment">
                        <button class="dropbtnComment"><img src='../View/Pictures/Icons/icons Options.PNG' alt='Options' width="45" height="45"></button>
                        <div class="dropdownComment-content">

                            <a href='../Controller/functions.php?deleteComment=<?php echo $commentId; ?>&url=<?php echo parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH); ?>&id=<?php echo $user; ?>'>Delete Comment</a>

                        </div>
                    </div>  
                </div>
                <?php
            }
            echo "<br>";
            ?>
        </div>

    </div>   
    <?php
    
}

# show all comments from post with option to comment the post
function showComments($postId){
    $user = getUserIdPost($postId);
    $commentIds = getCommentIdsofPost($postId);


    # displays Comments
    foreach ($commentIds as $commentId) {

        printComment($commentId);

    }

    # new Comment Input Form 
    ?>
    <form method="POST">
        <input type="hidden" name="postId" value="<?php echo $postId; ?>">
        <input type="hidden" name="user" value="<?php echo $user; ?>">
        <input type="hidden" name="url" value="<?php echo parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH); ?>">
        <input class="inputComment" type="text" name="newCommentText" maxlength=150 placeholder="Comment...">
        <button class="submitComment" type="submit" name="newCommentSubmit">></button>
    </form>

    <?php

}

# new Comment Process with SQL Injection
function createNewComment($postId, $userId, $content){
    global $conn;

    $content = mysqli_real_escape_string($conn, $content);

    $sql = "INSERT INTO `comment`(`postId`, `userId`, `content`) VALUES ($postId, $userId, '$content');";
    mysqli_query($conn, $sql);

}

#delete Comment with CommentID --> update on deleted
function deleteComment($commentId)
{
    global $conn;

    $sql = "UPDATE `comment` SET `deleted`= 1 WHERE `commentId`= '$commentId'";
    mysqli_query($conn, $sql);


}

# comment Post Process with SQL Injection
function commentPost($postId, $userId, $content){
    global $conn;

    $postIdInput = mysqli_real_escape_string($conn, $postId);
    $userIdInput = mysqli_real_escape_string($conn, $userId);
    $contentInput = mysqli_real_escape_string($conn, $content);

    $sqlComment = "INSERT INTO `comment`(`postId`, `userId`, `content`) VALUES ('$postIdInput','$userIdInput', '$contentInput')";
    mysqli_query($conn, $sqlComment);
}

# get Content of Comment
function getContentComment($commentId){
    global $conn;

    $sql = "SELECT content from comment WHERE commentId = $commentId";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    return $row['content'];
}

# get Number of Comments for a Post with PostID
function getCommentNumberPost($postId)
{
    global $conn;

    $sql = "SELECT comments FROM post WHERE postId =$postId";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['comments'];
}

# gets List of CommentIds from one Post
function getCommentIdsofPost($postId){
    global $conn;


    $commentIdArray = array();

    $sql = "SELECT commentId from comment WHERE postId = $postId AND deleted = 0 ORDER BY creationTime ASC;";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {

            $commentIdArray[] = $row['commentId'];


        }
    } else {

        # if Post got no Comments
        echo "<br>";
        echo "<div class='commentContainer'> <p>No Comments</p></div>";
        echo "<br>";

    }

    return $commentIdArray;
}

function getCreationTimeComment($commentId)
{
    global $conn;

    $sql = "SELECT * FROM comment WHERE commentId=$commentId;";
    $result = mysqli_query($conn, $sql);

    $row = mysqli_fetch_assoc($result);
    return $row['creationTime'];
}

function getUserIdComment($commentId){
    global $conn;

    $sql = "SELECT * FROM comment WHERE commentId=$commentId;";
    $result = mysqli_query($conn, $sql);

    $row = mysqli_fetch_assoc($result);
    return $row['userId'];
}

function getPostIdofComment($commentId){
    global $conn;

    $sql = "SELECT * FROM comment WHERE commentId=$commentId;";
    $result = mysqli_query($conn, $sql);

    $row = mysqli_fetch_assoc($result);
    return $row['postId'];
}


# Other Functions


function replaceLinebreaks($string) { 
    $string = preg_replace('/\v+|\\\r\\\n/Ui','<br/>', $string); 
    return $string; 
}

function replaceSpace($string){
    $string = str_replace(' ','_', $string); 
    return $string;
}

# get TimeDifference
function getTimeDif($postTime)
{
    # current Time in seconds
    $currentTime = time();
    # calculate timedifference in seconds, strtomite converts YYYY-MM-DD, HH:MM:SS to seconds
    $dif = $currentTime - strtotime($postTime);

    #in seconds
    if ($dif < 60) {  # 60 = 1 minute
        return "$dif seconds ago";
    }

    # in minute(s)
    if ($dif < 3600) {  # 3600 = 1 hour
        $dif = intval($dif / 60);

        if ($dif == 1) {
            return "$dif minute ago";
        } else {
            return "$dif minutes ago";
        }
    }

    # in hour(s)
    if ($dif < 86400) {    # 86400 = 1 day
        $dif = intval($dif / 3600);

        if ($dif == 1) {
            return "$dif hour ago";
        } else {
            return "$dif hours ago";
        }
    }

    # in day(s)
    if ($dif < 604800) { # 604800 = 1 week
        $dif = intval($dif / 86400);

        if ($dif == 1) {
            return "$dif day ago";
        } else {
            return "$dif days ago";
        }

    }

    # in week(s)
    if ($dif < 31536000) { # 31536000 = 1 year
        $dif = intval($dif / 604800);

        if ($dif == 1) {
            return "$dif week ago";
        } else {
            return "$dif weeks ago";
        }

    }

    # in years
    if (31536000 <= $dif) {
        $dif = intval($dif / 31536000);

        if ($dif == 1) {
            return "$dif year ago";
        } else {
            return "$dif years ago";
        }
    }
}

# logouts the User with session_destroy()
function logOut(){
    session_destroy();
    header('Location: ../View/LogIn.php');
}


# Buttons

function likeButton($value, $postId)
{


    if ($value == "like") {

        // Database entry 
        likePost($_SESSION['userId'], $postId);


        ?>
        <script>

            // Button Change
            document.getElementById("<?php echo $postId; ?>").value = "unlike";
            // document.getElementById("<?php echo $postId; ?>").style.backgroundColor = "red";
            document.getElementById("<?php echo $postId; ?>").innerHTML = "<img src='../View/Pictures/Icons/herzEnabled transparent.PNG' alt='Unliked' width='25' height='25'>";

        </script>

        <?php

    }

    if ($value == "unlike") {

        // Database entry
        unlikePost($_SESSION['userId'], $postId);

        ?>
        <script>

            // Button Change
            document.getElementById("<?php echo $postId; ?>").value = "like";
            // document.getElementById("<?php echo $postId; ?>").style.backgroundColor = "lightgrey";
            document.getElementById("<?php echo $postId; ?>").innerHTML = "<img src='../View/Pictures/Icons/herzDisabled transparent.PNG' alt='Unliked' width='25' height='25'>";
        </script>

        <?php

    }


}


# Not Used


#get posts from limitstart until limitstart + limit
function getLimitedPost($userId, $limitStart, $limit)
{

    $sqlPost = "SELECT * FROM `post` WHERE `userId` = '$userId' AND `deleted` = 0 ORDER BY `creationTime` DESC LIMIT $limitStart,$limit ";
    $resultPost = mysqli_query($GLOBALS["conn"], $sqlPost);
    return $resultPost;
}

#check if comment already liked and like or deLike
function checkLikeComment($userId, $commentId)
{

    $sqlCheckLike = "SELECT COUNT(commentId) as liked FROM `user_like_comment` WHERE userId = '$userId' AND commentId = '$commentId'";
    $resultCheckLike = mysqli_query($GLOBALS["conn"], $sqlCheckLike);
    $rowCheckLike = mysqli_fetch_assoc($resultCheckLike);
    //return $rowCheckLike['liked'];

    if ($rowCheckLike['liked'] == 0) { # 0 = not liked yet
        likeComment($userId, $commentId);
    } else {
        deLikeComment($userId, $commentId);
    }
}

# like Comment with UserId and postID
function likeComment($userId, $commentId)
{
    $sqlLike = "INSERT INTO `user_like_comment`(`userId`,`commentId`) VALUES ('$userId', $commentId)";
    mysqli_query($GLOBALS["conn"], $sqlLike);

}

# deLike Post with UserId and postID
function deLikeComment($userId, $commentId)
{
    $sqlDelike = "DELETE FROM `user_like_comment` WHERE `userId` = '$userId' AND `commentId` = $commentId";
    mysqli_query($GLOBALS["conn"], $sqlDelike);

}

# not needed anymore
# get Number of Likes for Comment with postID
function getLikeNumberComment($commentId)
{
    $sqlLikeNr = "SELECT COUNT(commentId) as number FROM `user_like_comment` WHERE `commentId` = '$commentId'";
    $resultLikeNr = mysqli_query($GLOBALS["conn"], $sqlLikeNr);
    $rowLikeNr = mysqli_fetch_assoc($resultLikeNr);
    return $rowLikeNr['number'];
}


?>
