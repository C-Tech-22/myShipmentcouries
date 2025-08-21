<?php
// Database configuration
$db_host = 'localhost';
$db_name = 'shipment';
$db_user = 'clever';
$db_pass = 'clever.ebinum28';

// Create database connection
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
  
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

// Function to sanitize user inputs
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to generate tracking number
function generate_tracking_number() {
    $prefix = 'CC';
    $timestamp = time();
    $random = rand(1000, 9999);
    return $prefix . $timestamp . $random;
}
?>