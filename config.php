<?php
// config.php
// This file connects our PHP pages to the MySQL database.
// Every page that needs the database will start with: require 'config.php';

$db_host = "localhost";   // WAMP's MySQL is on localhost
$db_user = "root";        // WAMP's default MySQL username
$db_pass = "";            // WAMP's default MySQL password (blank)
$db_name = "medivault";   // the database we created in phpMyAdmin

// Create the connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// If the connection fails, stop and show an error instead of a broken page
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Start a PHP session on every page that includes this file.
// Sessions let us remember "who is logged in" as the user moves between pages.
session_start();
?>