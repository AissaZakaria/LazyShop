<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}
// Get user information from session
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #FFF7E6; }
        .navbar { background-color: #FFA500; }
        .navbar-brand, .nav-link { color: white !important; }
        .container { max-width: 800px; }
        .intro-section { background-color: #FFEBCC; padding: 30px; border-radius: 8px; }
        .intro-text { font-size: 1.2rem; }
        .carousel-container { display: flex; justify-content: space-between; align-items: center; }
        .carousel-container .carousel { width: 45%; }
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
        <h1 class="text-center">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
        <p class="text-center">What would you like to do today?</p>

        <!-- Introduction Section with Carousel -->
        <div class="intro-section mb-5">
            <div class="row carousel-container">
                <!-- Paragraph -->
                <div class="col-6">
                    <p class="intro-text">
                        At LazyShop, we offer customized skins for your phone, laptop, and PlayStation. Our high-quality skins are designed to fit your device perfectly while adding a unique touch of style.
                        Browse our collection or create your own design today!
                    </p>
                </div>
                <!-- Carousel -->
                <div class="col-6">
                    <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <!-- Slide 1 -->
                            <div class="carousel-item active">
                                <img src="images/Logo ls.png" class="d-block w-100" alt="Slide 1">
                            </div>
                            <!-- Slide 2 -->
                            <div class="carousel-item">
                                <img src="images/1.png" class="d-block w-100" alt="Slide 2">
                            </div>
                            <!-- Slide 3 -->
                            <div class="carousel-item">
                                <img src="images/2.png" class="d-block w-100" alt="Slide 2">
                            </div>
                            <div class="carousel-item">
                                <img src="images/3.png" class="d-block w-100" alt="Slide 2">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Actions -->
        <div class="d-flex justify-content-center gap-4">
            <a href="chose_product.php" class="btn btn-primary">Create New Order</a>
            <a href="order_history.php" class="btn btn-secondary">View Order History</a>
            <a href="user.php" class="btn btn-success">User Profile</a>
        </div>
    </div>

    <footer class="text-center py-3 bg-dark text-white">
        <p>&copy; 2024 LazyShop. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
