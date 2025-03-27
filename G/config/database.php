<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'workaround_db');

// Attempt to connect to MySQL database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

// Check connection
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if(mysqli_query($conn, $sql)){
    mysqli_select_db($conn, DB_NAME);
} else {
    die("ERROR: Could not create database. " . mysqli_error($conn));
}
?>