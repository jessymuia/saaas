<?php

// Replace these values with your actual database credentials
$host = '127.0.0.1';      // e.g., 'localhost'
$port = '5432';      // e.g., 5432
$dbname = 'agent_property_management';  // e.g., 'your_database_name'
$user = 'kakaye';
$password = 'Terere90#';

try {
    // Connect to PostgreSQL
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password");

    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Perform a sample query
    $query = "SELECT version() as version";
    $result = $pdo->query($query);
    $row = $result->fetch(PDO::FETCH_ASSOC);

    // Display the PostgreSQL version
    echo "Connected to PostgreSQL. Server version: " . $row['version'];
} catch (PDOException $e) {
    // Display connection error
    echo "Connection failed: " . $e->getMessage();
}
?>
