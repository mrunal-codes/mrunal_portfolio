<?php
// Database Connection File

$servername = "localhost";   
$username   = "root";        
$password   = "";            
$dbname     = "portfolio_db"; 

// Connection banayein
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check karein ki connection sahi hai ya nahi
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>