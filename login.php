<?php
session_start();

// Check if the user is already logged in, if so, redirect to the appropriate page
if (isset($_SESSION['user'])) {
    // Redirect to the respective page based on privilege
    if ($_SESSION['user']['privilege'] == 'user') {
        header("Location: user.php");
    } elseif ($_SESSION['user']['privilege'] == 'admin') {
        header("Location: admin.php");
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'skinshop');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Use prepared statements to avoid SQL injection
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email); // "s" means string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password using password_verify
        if ($password === $user['password']) {  // Not using password_verify for simplicity (consider hashing passwords in production)
            // Store user in session
            $_SESSION['user'] = $user;

            // Redirect based on privilege
            if ($user['privilege'] == 'user') {
                header('Location: home.php');
            } elseif ($user['privilege'] == 'admin') {
                header('Location: admin.php');
            }
            exit();
        } else {
            $error_message = 'Incorrect email or password.';
        }
    } else {
        $error_message = 'No user found with this email.';
    }

    // Close connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f7f7f7; }
        .container { max-width: 400px; margin-top: 100px; }
        .btn-custom { background-color: #FFA500; color: white; }
    </style>
</head>
<body>

    <div class="container">
        <h2 class="text-center mb-4">Login</h2>

        <!-- Display error message if there is one -->
        <?php if (isset($error_message)) { ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php } ?>

        <form action="login.php" method="post">
            <!-- Email input -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control" id="email" name="email" required placeholder="Enter your email">
            </div>

            <!-- Password input -->
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
            </div>

            <!-- Login button -->
            <button type="submit" class="btn btn-custom w-100">Login</button>
        </form>

        <p class="text-center mt-3">Don't have an account? <a href="signup.php">Sign up here</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
