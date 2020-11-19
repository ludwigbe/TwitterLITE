
<?php
// Database Connection

$dbServername   = "localhost:3308";
$dbUsername     = "root";
$dbPassword     = "";
$dbName         = "twitterlitedb";

$conn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);
global $conn;


// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    
  }
  
?>