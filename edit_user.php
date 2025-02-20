<?php
session_start();

// Check if the user is an admin, if not, redirect to login
if (!isset($_SESSION['user']) || $_SESSION['user']['privilege'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'skinshop');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user data to edit
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $query = "SELECT * FROM users WHERE id = $user_id";
    $result = $conn->query($query);
    $user = $result->fetch_assoc();
} else {
    header('Location: admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $privilege = $_POST['privilege'];

    $update_query = "UPDATE users SET name='$name', email='$email', privilege='$privilege' WHERE id=$user_id";

    if ($conn->query($update_query) === TRUE) {
        header('Location: admin.php');
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h1>Edit User</h1>
        <form action="edit_user.php?id=<?php echo $user['id']; ?>" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" name="name" id="name" value="<?php echo $user['name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email" value="<?php echo $user['email']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="privilege" class="form-label">Privilege</label>
                <select name="privilege" class="form-select" required>
                    <option value="user" <?php echo $user['privilege'] == 'user' ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo $user['privilege'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</body>
</html>
