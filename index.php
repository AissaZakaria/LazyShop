<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LazyShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #FFF7E6; }
        .btn-orange { background-color: #FFA500; color: white; }
        .navbar { background-color: #FFA500; }
        .navbar-brand, .nav-link { color: white !important; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">LazyShop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="signup.php">Sign Up</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="text-center text-white py-5" style="background-color: #FFA500;">
        <div class="container">
            <h1>Welcome to LazyShop</h1>
            <p>Your destination for premium custom skins!</p>
            <a href="login.php" class="btn btn-light btn-lg mt-3">Order Now</a>
        </div>
    </header>

    <footer class="text-center py-3 bg-dark text-white">
    <p>&copy; 2024 LazyShop. All rights reserved.</p>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
