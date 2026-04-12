<?php

// Database settings
$host     = "localhost";      // XAMPP runs MySQL on localhost
$dbname   = "placement_portal"; // The database we just created
$username = "root";           // Default XAMPP MySQL username
$password = "";               // Default XAMPP MySQL password is empty

// Create connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check if connection worked
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// If you reach here, connection is successful
?>