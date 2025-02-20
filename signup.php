<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'skinshop');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the user already exists
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $error_message = 'An account with this email already exists.';
    } else {
        // Set default privilege to 'user'
        $privilege = 'user';

        // Insert the new user into the database
        $insert_query = "INSERT INTO users (email, password, privilege) VALUES ('$email', '$password', '$privilege')";
        if ($conn->query($insert_query) === TRUE) {
            $success_message = 'Account created successfully!';
        } else {
            $error_message = 'Error creating account: ' . $conn->error;
        }
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f7f7f7; }
        .container { max-width: 400px; margin-top: 100px; }
        .btn-custom { background-color: #FFA500; color: white; }
    </style>
</head>
<body>

    <div class="container">
        <h2 class="text-center mb-4">Sign Up</h2>

        <form action="signup.php" method="post">
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

            <!-- Error or Success message -->
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php elseif (isset($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <!-- Signup button -->
            <button type="submit" class="btn btn-custom w-100">Sign Up</button>
        </form>

        <p class="text-center mt-3">Already have an account? <a href="login.php">Login here</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
