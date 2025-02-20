<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}
// Check if the user is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['privilege'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'skinshop');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle user deletion
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    $delete_query = "DELETE FROM users WHERE id = $user_id";

    if ($conn->query($delete_query) === TRUE) {
        header('Location: admin.php');
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
