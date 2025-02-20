<?php  
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Product Type</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #FFF7E6; }
        .navbar { background-color: #FFA500; }
        .navbar-brand { color: white !important; }
        .container { max-width: 600px; margin-top: 50px; }
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
        <h2 class="text-center">Select Product Type</h2>

        <form action="create_order.php" method="GET">
            <div class="mb-3">
                <label for="product_type" class="form-label">Product Type</label>
                <select name="product_type" id="product_type" class="form-select" required>
                    <option value="" disabled selected>Select a product type</option>
                    <option value="laptop">Laptop</option>
                    <option value="phone">Phone</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Next</button>
        </form>
    </div>

    <footer class="text-center py-3 bg-dark text-white mt-5">
        <p>&copy; 2024 LazyShop. All rights reserved.</p>
    </footer>
</body>
</html>
