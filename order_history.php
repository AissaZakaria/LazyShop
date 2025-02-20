<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "skinshop";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');  // Redirect to login page if not logged in
    exit;
}

$user_id = $_SESSION['user']['id'];  // Make sure this matches your session structure

// Fetch the order history for the logged-in user
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #FFF7E6; }
        .navbar { background-color: #FFA500; }
        .navbar-brand, .nav-link { color: white !important; }
        .container { max-width: 800px; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">LazyShop</a>
            <div class="d-flex">
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <h2>Your Order History</h2>

        <!-- Table displaying order history -->
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Order #</th>
                    <th scope="col">Product Type</th>
                    <th scope="col">Brand</th>
                    <th scope="col">Model</th>
                    <th scope="col">Payment Method</th>
                    <th scope="col">Status</th>
                    <th scope="col">Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop through and display each order
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['product_type']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['brand']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['model']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['payment_method']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No orders found</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <a href="home.php" class="btn btn-secondary">Back to Home</a>
    </div>

    <footer class="text-center py-3 bg-dark text-white">
        <p>&copy; 2024 LazyShop. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
