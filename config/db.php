<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";     // Server name
$username   = "root";          // MySQL username (XAMPP default)
$password   = "";              // MySQL password (XAMPP mein khali)
$dbname     = "portfolio_db";  // Aapke database ka naam

$conn = mysqli_connect($servername, $username, $password, $dbname);


if (!$conn)
     {
    die("❌ Database Connection Failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
date_default_timezone_set('Asia/Kolkata');
?>