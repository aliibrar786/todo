<?php
// admin.php
include 'credential.php';

// Simple authentication
$admin_user = "admin";
$admin_pass = "admin123";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === $admin_user && $password === $admin_pass) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $login_error = "Invalid credentials";
    }
}

$is_logged_in = $_SESSION['admin_logged_in'] ?? false;

// Get statistics
$total_reservations = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM reservations"))['total'];
$today_reservations = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM reservations WHERE visit_date = CURDATE()"))['total'];
$pending_reservations = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM reservations WHERE status = 'pending'"))['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border: 2px solid #c9a769;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #c9a769;
            display: block;
        }
        
        .stat-label {
            color: #fff;
            font-size: 0.9rem;
        }
        
        .admin-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .action-btn {
            background: #c9a769;
            color: #000;
            padding: 12px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            background: #b8935a;
            transform: translateY(-2px);
        }
        
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 40px;
            background: rgba(0, 0, 0, 0.9);
            border-radius: 15px;
            border: 2px solid #c9a769;
        }
        
        .login-container h2 {
            text-align: center;
            color: #c9a769;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!$is_logged_in): ?>
            <div class="login-container">
                <h2>⚙️ Admin Login</h2>
                <?php if (isset($login_error)): ?>
                    <div class="error-message"><?php echo $login_error; ?></div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn-submit" style="width: 100%;">Login</button>
                </form>
                <p style="text-align: center; margin-top: 20px;">
                    <a href="index.php" style="color: #c9a769;">← Back to Home</a>
                </p>
            </div>
        <?php else: ?>
            <div class="header">
                <h1>⚙️ Admin Dashboard</h1>
                <p><a href="index.php" class="action-btn">←Back To Home</a>  
               <a href="index.php" class="action-btn">Logout</a> </p>
                

                <!-- <p><a href="index.php" style="color: #c9a769;">← Back to Home</a> | 
                   <a href="?logout=1" style="color: #ff6b6b;">Logout</a></p> -->
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-number"><?php echo $total_reservations; ?></span>
                    <span class="stat-label">Total Reservations</span>
                </div>
                
                <div class="stat-card">
                    <span class="stat-number"><?php echo $today_reservations; ?></span>
                    <span class="stat-label">Today's Reservations</span>
                </div>
                
                <div class="stat-card">
                    <span class="stat-number"><?php echo $pending_reservations; ?></span>
                    <span class="stat-label">Pending Confirmations</span>
                </div>
                
                <div class="stat-card">
                    <span class="stat-number">50</span>
                    <span class="stat-label">Total Tables</span>
                </div>
            </div>

            <div class="admin-actions">
                <a href="view_reservations.php" class="action-btn">View All Bookings</a>
                <a href="view_reservations.php?date=<?php echo date('Y-m-d'); ?>" class="action-btn">Today's Bookings</a>
                <a href="#" class="action-btn">Manage Tables</a>
                <a href="reports.php" class="action-btn">Reports</a>
                <a href="customers.php" class="action-btn">Customer List</a>
                
            </div>

            <div class="form-container" style="margin-top: 30px;">
                <h2>📊 Recent Reservations</h2>
                <?php
                $recent_sql = "SELECT * FROM reservations ORDER BY booking_date DESC LIMIT 5";
                $recent_result = mysqli_query($conn, $recent_sql);
                ?>
                
                <table style="width: 100%; background: rgba(255,255,255,0.95); border-radius: 8px; overflow: hidden;">
                    <thead>
                        <tr>
                            <th style="padding: 12px; background: #c9a769; color: #000;">ID</th>
                            <th style="padding: 12px; background: #c9a769; color: #000;">Customer</th>
                            <th style="padding: 12px; background: #c9a769; color: #000;">Date/Time</th>
                            <th style="padding: 12px; background: #c9a769; color: #000;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($recent_result)): ?>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 10px;"><?php echo $row['reservation_id']; ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($row['customer_name']); ?></td>
                            <td style="padding: 10px;"><?php echo date('M d, h:i A', strtotime($row['booking_date'])); ?></td>
                            <td style="padding: 10px;">
                                <span class="status <?php echo $row['status']; ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if (isset($_GET['logout'])) {
        session_destroy();
        header('Location: admin.php');
        exit;
    } ?>
</body>
</html>