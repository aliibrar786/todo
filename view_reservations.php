<?php
// view_reservations.php
// This file displays all reservations for a selected date

// Include database connection file
// This contains database credentials and establishes connection
include 'credential.php';

// Get the selected date from URL parameter, default to today's date if not provided
// The ?? operator is null coalescing operator (PHP 7+)
// $_GET['date'] comes from the date filter form
$selected_date = $_GET['date'] ?? date('Y-m-d');

// Initialize an empty array to store reservation data
$reservations = [];

// Fetch reservations for the selected date
// Using prepared statement to prevent SQL injection
// ? is a placeholder that will be replaced with actual value
$sql = "SELECT * FROM reservations WHERE visit_date = ? ORDER BY visit_time";
$stmt = mysqli_prepare($conn, $sql); // Prepare the SQL statement

// Bind parameters to the prepared statement
// "s" indicates the parameter is a string type
// $selected_date is the actual value that replaces the ?
mysqli_stmt_bind_param($stmt, "s", $selected_date);

// Execute the prepared statement
mysqli_stmt_execute($stmt);

// Get the result set from the executed statement
$result = mysqli_stmt_get_result($stmt);

// Loop through each row in the result set and add to reservations array
// mysqli_fetch_assoc() returns each row as an associative array
while ($row = mysqli_fetch_assoc($result)) {
    $reservations[] = $row; // Add each reservation to the array
}

// Close the prepared statement to free resources
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- Responsive viewport for mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reservations - Restaurant System</title>
    <!-- Link to external CSS file for styling -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Main container div for centering content -->
    <div class="container">
        <!-- Header section with title and back button -->
        <div class="header">
            <h1>
                <!-- Display calendar emoji and formatted date -->
                📋 Reservations for <?php 
                    // Format date from YYYY-MM-DD to "Month Day, Year"
                    // strtotime() converts string to timestamp
                    // date() formats timestamp to readable format
                    echo date('F j, Y', strtotime($selected_date)); 
                ?>
            </h1>
            <!-- Back button to return to home page -->
            <p><a href="index.php" class="action-btn">←Back To Home</a></p>
        </div>

        <!-- Date filter section for selecting different dates -->
        <div class="date-filter">
            <!-- Form uses GET method to filter by date -->
            <!-- action="" means form submits to same page -->
            <form method="GET" action="">
                <label for="date" style="color: #c9a769; font-weight: bold;">
                    Select Date:
                </label>
                <!-- Date input field, pre-filled with selected date -->
                <input type="date" id="date" name="date" 
                       value="<?php echo $selected_date; ?>">
                <!-- Submit button to filter reservations -->
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>

        <!-- Main table container for displaying reservations -->
        <div class="reservations-table">
            <?php if (empty($reservations)): ?>
                <!-- Show message if no reservations found -->
                <div style="padding: 40px; text-align: center; color: #666;">
                    <h3>No reservations found for this date.</h3>
                </div>
            <?php else: ?>
                <!-- Display table if reservations exist -->
                <table>
                    <!-- Table header with column names -->
                    <thead>
                        <tr>
                            <th>ID</th>              <!-- Reservation ID -->
                            <th>Customer</th>        <!-- Customer Name -->
                            <th>Phone</th>           <!-- Phone Number -->
                            <th>Time</th>            <!-- Visit Time -->
                            <th>Persons</th>         <!-- Number of People -->
                            <th>Table</th>           <!-- Table Number -->
                            <th>Type</th>            <!-- Table Type -->
                            <th>Payment</th>         <!-- Payment Status -->
                            <th>Status</th>          <!-- Booking Status -->
                        </tr>
                    </thead>
                    <!-- Table body with reservation data -->
                    <tbody>
                        <?php foreach ($reservations as $reservation): ?>
                        <!-- Loop through each reservation in the array -->
                        <tr>
                            <!-- Display reservation ID -->
                            <td><?php echo $reservation['reservation_id']; ?></td>
                            
                            <!-- Display customer name with security protection -->
                            <!-- htmlspecialchars() prevents XSS attacks -->
                            <td>
                                <strong>
                                    <?php echo htmlspecialchars($reservation['customer_name']); ?>
                                </strong>
                            </td>
                            
                            <!-- Display phone number with security protection -->
                            <td><?php echo htmlspecialchars($reservation['phone']); ?></td>
                            
                            <!-- Format time from 24-hour to 12-hour format -->
                            <!-- Example: 14:30:00 becomes 02:30 PM -->
                            <td>
                                <?php 
                                    echo date('h:i A', strtotime($reservation['visit_time'])); 
                                ?>
                            </td>
                            
                            <!-- Display number of persons -->
                            <td><?php echo $reservation['number_of_persons']; ?></td>
                            
                            <!-- Display table number -->
                            <td><?php echo $reservation['table_no']; ?></td>
                            
                            <!-- Display table type (Window Side, Family, etc.) -->
                            <td><?php echo $reservation['table_type']; ?></td>
                            
                            <!-- Display payment status (Pending, Paid, Partial) -->
                            <td><?php echo $reservation['payment_status']; ?></td>
                            
                            <!-- Display booking status with CSS styling -->
                            <td>
                                <!-- Status badge with dynamic CSS class -->
                                <!-- Example: class="status pending" -->
                                <span class="status <?php echo $reservation['status']; ?>">
                                    <!-- ucfirst() capitalizes first letter -->
                                    <!-- Example: "pending" becomes "Pending" -->
                                    <?php echo ucfirst($reservation['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <!-- End of foreach loop -->
                    </tbody>
                </table>
            <?php endif; ?>
            <!-- End of if-else statement -->
        </div>

        <!-- Footer with reservation count and date -->
        <div class="footer">
            <p>
                <!-- Display total number of reservations -->
                Total Reservations: <?php 
                    // count() returns number of elements in array
                    echo count($reservations); 
                ?> | 
                
                <!-- Display the selected date in readable format -->
                Date: <?php 
                    echo date('F j, Y', strtotime($selected_date)); 
                ?>
            </p>
        </div>
    </div>
    <!-- End of main container -->
</body>
</html>