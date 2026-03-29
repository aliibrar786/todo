<?php
include 'credential.php';

$sql = "SELECT customer_name, phone, COUNT(*) AS visits 
        FROM reservations 
        GROUP BY customer_name, phone
        ORDER BY visits DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container customer-list-container">
        <h1>👥 Customer List</h1>

        <table>
                <tr>
                <th>Name</th>
                <th>Phone</th>
                <th>Total Visits</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                <td><?php echo $row['visits']; ?></td>
            </tr>
            <?php endwhile; ?>
        </table>

      <a href="index.php" class="action-btn">←BACK</a>
  </div>
</body>
