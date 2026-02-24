<?php
// config/db.php
// Database connection parameters
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'aspa_fitness';

// create connection
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// make charset utf8
$conn->set_charset('utf8');
