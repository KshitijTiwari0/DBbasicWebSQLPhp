<?php
$db_host = "localhost"; // Your database host
$db_user = "your_database_username"; 
$db_password = "your_database_password"; 
$db_name = "your_database_name"; 

// Create the connection
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

// Check for connection errors
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} 
?>
