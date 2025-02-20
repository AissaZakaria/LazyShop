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

$servername = "localhost";  // Database server
$username = "root";         // Database username
$password = "";             // Database password
$dbname = "skinshop";       // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If the form is submitted, update the user data in the database
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input values
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));

    // Check if the email contains "@" symbol
    if (strpos($email, '@') === false) {
        $errorMessage = "Invalid email format. '@' symbol is required.";
    } else {
        // Update user information in the database
        $sql = "UPDATE users SET name=?, email=?, phone_number=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $phone_number, $user['id']); // Correct binding
        
        if ($stmt->execute()) {
            // Update the session with new data
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['email'] = $email;
            $_SESSION['user']['phone_number'] = $phone_number;

            // Check if password is being updated
            if (!empty($_POST['password'])) {
                $newPassword = $_POST['password'];

                // Update the password in the database (without hashing)
                $passwordUpdateSql = "UPDATE users SET password=? WHERE id=?";
                $passwordStmt = $conn->prepare($passwordUpdateSql);
                $passwordStmt->bind_param("si", $newPassword, $user['id']);
                $passwordStmt->execute();

                // Update session with new password (store the plain password)
                $_SESSION['user']['password'] = $newPassword;
            }

            // Redirect to the same page with a success message
            header("Location: user.php?success=true");
            exit;
        } else {
            $errorMessage = "Error updating profile. Please try again.";
        }
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
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
        <h1 class="text-center">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>

        <!-- Success or Error Message -->
        <?php if (isset($successMessage)) { ?>
            <div class="alert alert-success"><?php echo $successMessage; ?></div>
        <?php } elseif (isset($errorMessage)) { ?>
            <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
        <?php } ?>

        <h2>Your Profile</h2>
        <form method="POST" action="user.php">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
            </div>

            <h3>Change Password</h3>
            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="password" name="password">
                <small class="form-text text-muted">Leave blank if you do not wish to change your password.</small>
            </div>

            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>

        <!-- Back to Home Button -->
        <div class="mt-3 text-center">
            <a href="home.php" class="btn btn-secondary">Back to Home</a>
        </div>
    </div>

    <footer class="text-center py-3 bg-dark text-white">
        <p>&copy; 2024 LazyShop. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
