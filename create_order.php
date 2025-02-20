<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id']; // User ID from session

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "skinshop";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the product type from the URL
$product_type = isset($_GET['product_type']) ? $_GET['product_type'] : '';
if (!$product_type) {
    $_SESSION['message'] = "Please select a product type.";
    header("Location: choose_product.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $photo_link = $_POST['photo_link'];
    $theme_request = $_POST['theme_request'];
    $comments = $_POST['comments'];
    $payment_method = $_POST['payment_method'];

    $user_id = $_SESSION['user']['id']; // Corrected this line
    $created_at = date("Y-m-d H:i:s");
    $updated_at = date("Y-m-d H:i:s");

    // Insert the order into the database
    $sql = "INSERT INTO orders (user_id, product_type, brand, model, photo_link, theme_request, comments, payment_method, status, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssss", $user_id, $product_type, $brand, $model, $photo_link, $theme_request, $comments, $payment_method, $created_at, $updated_at);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Order is pending. Thank you for buying from LazyShop!";

        // Show message and redirect after 2 seconds
        echo "<div class='alert alert-success text-center mt-5'>".$_SESSION['message']."</div>";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'home.php';
                }, 2000); // Redirect after 2 seconds
              </script>";
        exit();
    } else {
        $_SESSION['message'] = "Failed to create the order.";
    }
}

// Fetch available brands (marks) for the selected product type
$marks = [];
$sql = "SELECT * FROM marks WHERE product_type = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $product_type);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $marks[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #FFF7E6; }
        .navbar { background-color: #FFA500; }
        .navbar-brand { color: white !important; }
        .container { max-width: 600px; margin-top: 50px; }
        footer { background-color: #333; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="#">LazyShop</a>
        </div>
    </nav>

    <div class="container">
        <h2 class="text-center">Create New Order</h2>

        <form action="create_order.php?product_type=<?php echo htmlspecialchars($product_type); ?>" method="POST">
            <!-- Hidden fields for user_id, product_type, created_at, updated_at -->
            <input type="hidden" name="created_at" value="<?php echo date("Y-m-d H:i:s"); ?>">
            <input type="hidden" name="updated_at" value="<?php echo date("Y-m-d H:i:s"); ?>">

            <div class="mb-3">
                <label for="product_type" class="form-label">Product Type</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($product_type); ?>" disabled>
            </div>

            <div class="mb-3">
                <label for="brand" class="form-label">Select Brand</label>
                <select name="brand" id="brand" class="form-select" required>
                    <?php if (empty($marks)): ?>
                        <option value="" disabled>No brands available for this product type</option>
                    <?php else: ?>
                        <?php foreach ($marks as $mark): ?>
                            <option value="<?php echo $mark['name']; ?>"><?php echo $mark['name']; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="model" class="form-label">Model</label>
                <input type="text" name="model" id="model" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="photo_link" class="form-label">Photo Link</label>
                <input type="text" name="photo_link" id="photo_link" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="theme_request" class="form-label">Theme Request</label>
                <input type="text" name="theme_request" id="theme_request" class="form-control">
            </div>

            <div class="mb-3">
                <label for="comments" class="form-label">Comments</label>
                <textarea name="comments" id="comments" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label for="payment_method" class="form-label">Payment Method</label>
                <select name="payment_method" id="payment_method" class="form-select" required>
                    <option value="cash">Cash</option>
                    <option value="d17">d17</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Submit Order</button>
        </form>
    </div>

    <footer class="text-center py-3 text-white mt-5">
        <p>&copy; 2024 LazyShop. All rights reserved.</p>
    </footer>

    <!-- Optional Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
