<?php
session_start();

// Start output buffering to prevent "headers already sent" error
ob_start();

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

// Fetch pending orders with user names and prices
$query_pending_orders = "SELECT orders.id, orders.product_type, users.name AS user_name, 
                                CASE orders.product_type
                                    WHEN 'laptop' THEN 50
                                    WHEN 'playstation' THEN 45
                                    WHEN 'phone' THEN 28
                                END AS price
                         FROM orders 
                         JOIN users ON orders.user_id = users.id 
                         WHERE orders.status = 'pending'";

$result_pending_orders = $conn->query($query_pending_orders);

// Fetch delivered orders (updated from approved)
$query_delivered_orders = "SELECT orders.id, orders.product_type, users.name AS user_name, 
                                      CASE orders.product_type
                                          WHEN 'laptop' THEN 50
                                          WHEN 'playstation' THEN 45
                                          WHEN 'phone' THEN 28
                                      END AS price
                           FROM orders
                           JOIN users ON orders.user_id = users.id
                           WHERE orders.status = 'delivered'";

$result_delivered_orders = $conn->query($query_delivered_orders);

// Fetch pending orders' count and worth
$query_pending = "SELECT COUNT(*) as count, SUM(CASE product_type 
        WHEN 'laptop' THEN 50
        WHEN 'playstation' THEN 45
        WHEN 'phone' THEN 28
        END) as worth
        FROM orders WHERE status = 'pending'";
$result_pending = $conn->query($query_pending);
$pending = $result_pending->fetch_assoc();

// Fetch delivered orders' count and worth
$query_done = "SELECT COUNT(*) as count, SUM(CASE product_type 
        WHEN 'laptop' THEN 50
        WHEN 'playstation' THEN 45
        WHEN 'phone' THEN 28
        END) as worth
        FROM orders WHERE status = 'delivered'";
$result_done = $conn->query($query_done);
$done = $result_done->fetch_assoc();

// Fetch users (for user management)
$query_users = "SELECT id, name, email, privilege FROM users";
$result_users = $conn->query($query_users);

// Close connection
$conn->close();

// Handling privilege change
if (isset($_GET['user_id']) && isset($_GET['new_privilege'])) {
    $user_id = $_GET['user_id'];
    $new_privilege = $_GET['new_privilege'];

    $conn = new mysqli('localhost', 'root', '', 'skinshop');
    $query_update_privilege = "UPDATE users SET privilege = '$new_privilege' WHERE id = $user_id";
    $conn->query($query_update_privilege);
    $conn->close();

    // Redirect to refresh the page after privilege change
    header('Location: admin.php');
    exit;
}

// Remove user
if (isset($_GET['remove_user'])) {
    $user_id = $_GET['remove_user'];

    $conn = new mysqli('localhost', 'root', '', 'skinshop');
    $query_remove_user = "DELETE FROM users WHERE id = $user_id";
    $conn->query($query_remove_user);
    $conn->close();

    // Redirect after removing user
    header('Location: admin.php');
    exit;
}

// Handle Approve Order and Cancel Order
if (isset($_GET['approve_order'])) {
    $order_id = $_GET['approve_order'];

    $conn = new mysqli('localhost', 'root', '', 'skinshop');
    $query_approve_order = "UPDATE orders SET status = 'delivered' WHERE id = $order_id";
    $conn->query($query_approve_order);
    $conn->close();

    // Redirect after approving the order
    header('Location: admin.php');
    exit;
}

if (isset($_GET['cancel_order'])) {
    $order_id = $_GET['cancel_order'];

    $conn = new mysqli('localhost', 'root', '', 'skinshop');
    $query_cancel_order = "DELETE FROM orders WHERE id = $order_id";
    $conn->query($query_cancel_order);
    $conn->close();

    // Redirect after cancelling the order
    header('Location: admin.php');
    exit;
}

// Handle Print Order PDF
if (isset($_GET['print_order'])) {
    require('fpdf/fpdf.php');
    $order_id = $_GET['print_order'];

    // Create database connection
    $conn = new mysqli('localhost', 'root', '', 'skinshop');
    
    // Fetch order details
    $query_order_details = "SELECT orders.id, orders.product_type, orders.status, 
                            users.name AS user_name, users.email, 
                            CASE orders.product_type
                                WHEN 'laptop' THEN 50
                                WHEN 'playstation' THEN 45
                                WHEN 'phone' THEN 28
                            END AS price
                            FROM orders 
                            JOIN users ON orders.user_id = users.id 
                            WHERE orders.id = $order_id";
    $result_order_details = $conn->query($query_order_details);
    $order = $result_order_details->fetch_assoc();
    $conn->close();

    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    // Title
    $pdf->Cell(190, 10, 'Order Details', 0, 1, 'C');
    $pdf->Ln(10);

    // Set table headers
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(90, 10, 'Order ID: ' . $order['id'], 1, 0, 'L');
    $pdf->Cell(90, 10, 'User Name: ' . $order['user_name'], 1, 1, 'L');
    
    $pdf->Cell(90, 10, 'Email: ' . $order['email'], 1, 0, 'L');
    $pdf->Cell(90, 10, 'Product Type: ' . ucfirst($order['product_type']), 1, 1, 'L');
    
    $pdf->Cell(90, 10, 'Price: ' . number_format($order['price'], 2) . ' TND', 1, 0, 'L');
    $pdf->Cell(90, 10, 'Status: ' . ucfirst($order['status']), 1, 1, 'L');

    // Output the PDF
    $pdf->Output();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #FFF7E6; }
        .navbar { background-color: #FFA500; }
        .navbar-brand, .nav-link { color: white !important; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">LazyShop Admin</a>
            <div class="d-flex">
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <h1 class="text-center">Admin Dashboard</h1>
        <p class="text-center">Manage users and view order statistics.</p>

        <!-- User Management -->
        <h3>User Management</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Privilege</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $result_users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['name']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td>
                            <!-- Privilege Dropdown -->
                            <form method="GET" action="admin.php" style="display: inline;">
                                <select name="new_privilege" class="form-select" onchange="this.form.submit()">
                                    <option value="user" <?php if ($user['privilege'] == 'user') echo 'selected'; ?>>User</option>
                                    <option value="admin" <?php if ($user['privilege'] == 'admin') echo 'selected'; ?>>Admin</option>
                                </select>
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            </form>
                        </td>
                        <td>
                            <a href="admin.php?remove_user=<?php echo $user['id']; ?>" class="btn btn-danger">Remove</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Order Statistics -->
        <h3>Order Statistics</h3>
        <div class="row">
            <div class="col-md-6">
                <h4>Pending Orders</h4>
                <p>Count: <?php echo $pending['count']; ?> | Worth: <?php echo number_format($pending['worth'], 2); ?> TND</p>
            </div>
            <div class="col-md-6">
                <h4>Delivered Orders</h4>
                <p>Count: <?php echo $done['count']; ?> | Worth: <?php echo number_format($done['worth'], 2); ?> TND</p>
            </div>
        </div>

        <!-- Pending Orders Table -->
        <h3>Pending Orders</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User Name</th>
                    <th>Product Type</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $result_pending_orders->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td><?php echo $order['user_name']; ?></td>
                        <td><?php echo ucfirst($order['product_type']); ?></td>
                        <td><?php echo number_format($order['price'], 2); ?> TND</td>
                        <td>
                            <a href="admin.php?approve_order=<?php echo $order['id']; ?>" class="btn btn-success">Approve</a>
                            <a href="admin.php?cancel_order=<?php echo $order['id']; ?>" class="btn btn-danger">Cancel</a>
                            <a href="admin.php?print_order=<?php echo $order['id']; ?>" class="btn btn-primary">Print</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Delivered Orders Table -->
        <h3>Delivered Orders</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User Name</th>
                    <th>Product Type</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $result_delivered_orders->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td><?php echo $order['user_name']; ?></td>
                        <td><?php echo ucfirst($order['product_type']); ?></td>
                        <td><?php echo number_format($order['price'], 2); ?> TND</td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
