<?php
// index.php - Main Reservation Form
// This is the homepage of the restaurant reservation system

// Include database connection file
// credential.php contains database configuration and connection setup
include 'credential.php';

// Initialize variables for error handling and success messages
// $errors array will store validation error messages
// $success string will store success message
$errors = [];
$success = '';

// Check if form was submitted using POST method
// $_SERVER['REQUEST_METHOD'] checks the HTTP request method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // FORM DATA COLLECTION WITH VALIDATION
    
    // Collect and sanitize form data using mysqli_real_escape_string()
    // This prevents SQL injection attacks
    // The ?? operator (null coalescing) provides default values if fields are empty
    
    // Customer personal information
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name'] ?? '');
    $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    
    // Booking details
    $visit_date = mysqli_real_escape_string($conn, $_POST['visit_date'] ?? '');
    $visit_time = mysqli_real_escape_string($conn, $_POST['visit_time'] ?? '');
    
    // Convert to appropriate data types
    $number_of_persons = intval($_POST['number_of_persons'] ?? 0); // Convert to integer
    $advance_paid = floatval($_POST['advance_paid'] ?? 0); // Convert to float/decimal
    
    // Table selection
    $table_no = mysqli_real_escape_string($conn, $_POST['table_no'] ?? '');
    $table_type = mysqli_real_escape_string($conn, $_POST['table_type'] ?? '');
    
    // Additional information
    $occasion = mysqli_real_escape_string($conn, $_POST['occasion'] ?? '');
    $special_request = mysqli_real_escape_string($conn, $_POST['special_request'] ?? '');
    
    // Payment and booking information
    $payment_status = mysqli_real_escape_string($conn, $_POST['payment_status'] ?? 'pending');
    $booking_source = mysqli_real_escape_string($conn, $_POST['booking_source'] ?? 'Website');
    
    // ======================
    // FORM VALIDATION
    // ======================
    
    // Check required fields are not empty
    if (empty($customer_name)) $errors[] = "Customer name is required";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (empty($visit_date)) $errors[] = "Visit date is required";
    if (empty($visit_time)) $errors[] = "Visit time is required";
    
    // Validate numeric fields
    if ($number_of_persons < 1) $errors[] = "Number of persons must be at least 1";
    
    // Validate table selection
    if (empty($table_no)) $errors[] = "Table number is required";
    
    // ======================
    // DATABASE INSERTION
    // ======================
    
    // Only proceed if there are no validation errors
    if (empty($errors)) {
        // SQL query with prepared statement
        // Using ? placeholders to prevent SQL injection
        // Default values: status='pending', created_by='Customer'
        $sql = "INSERT INTO reservations (customer_name, phone, email, visit_date, visit_time, 
                number_of_persons, table_no, table_type, occasion, special_request, 
                advance_paid, payment_status, booking_source, status, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'Customer')";
        
        // Prepare the SQL statement
        $stmt = mysqli_prepare($conn, $sql);
        
        // Bind parameters to the prepared statement
        // "sssssissssdss" defines the data types:
        // s = string, i = integer, d = double/float
        mysqli_stmt_bind_param($stmt, "sssssissssdss", 
            $customer_name, $phone, $email, $visit_date, $visit_time,
            $number_of_persons, $table_no, $table_type, $occasion, $special_request,
            $advance_paid, $payment_status, $booking_source);
        
        // Execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Success message
            $success = "Reservation submitted successfully! Your booking is pending confirmation.";
        } else {
            // Database error
            $errors[] = "Error: " . mysqli_error($conn);
        }
        
        // Close the prepared statement
        mysqli_stmt_close($stmt);
    }
}
// End of PHP processing
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- Responsive design for mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Table Reservation System</title>
    
    <!-- External CSS stylesheet -->
    <link rel="stylesheet" href="style.css">
    
    <!-- Google Fonts for typography -->
    <!-- Preconnect for performance optimization -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Importing Chivo and Press Start 2P fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Chivo:ital,wght@0,100..900;1,100..900&family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Main container for centering content -->
    <div class="container">
        
        <!-- Page Header -->
        <div class="header">
            <h1>🍽️ Restaurant Table Reservation System</h1>
            <!-- Emoji adds visual appeal -->
        </div>

        <!-- ======================
             ERROR MESSAGES DISPLAY
             ====================== -->
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <strong>Please correct the following errors:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- ======================
             SUCCESS MESSAGE DISPLAY
             ====================== -->
        <?php if ($success): ?>
            <div class="success-message">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <!-- ======================
             RESERVATION FORM
             ====================== -->
        <div class="form-container">
            <h2>📅 Make a Reservation</h2>
            
            <!-- Reservation Form -->
            <!-- action="" means form submits to same page -->
            <!-- method="POST" for secure data transmission -->
            <form class="reservation-form" method="POST" action="">
                
                <!-- ======================
                     SECTION 1: PERSONAL DETAILS
                     ====================== -->
                <div class="form-group">
                    <label for="customer_name">Full Name *</label>
                    <input type="text" id="customer_name" name="customer_name" required>
                    <!-- required attribute enables browser validation -->
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" required 
                           placeholder="XXXX-XXXX-XX">
                    <!-- type="tel" for telephone number input -->
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" 
                           placeholder="username@gmail.com">
                    <!-- type="email" enables email validation -->
                </div>

                <!-- ======================
                     SECTION 2: BOOKING DETAILS
                     ====================== -->
                <div class="form-group">
                    <label for="visit_date">Visit Date *</label>
                    <input type="date" id="visit_date" name="visit_date" required 
                           min="<?php echo date('Y-m-d'); ?>">
                    <!-- min attribute prevents past dates -->
                    <!-- PHP dynamically sets today's date as minimum -->
                </div>

                <div class="form-group">
                    <label for="visit_time">Visit Time *</label>
                    <input type="time" id="visit_time" name="visit_time" required>
                    <!-- type="time" shows time picker -->
                </div>

                <div class="form-group">
                    <label for="number_of_persons">Number of Persons *</label>
                    <input type="number" id="number_of_persons" name="number_of_persons" 
                           min="1" max="20" required placeholder="at least one">
                    <!-- min/max constraints for number input -->
                </div>

                <!-- ======================
                     SECTION 3: TABLE SELECTION
                     ====================== -->
                <div class="form-group">
                    <label for="table_type">Table Type *</label>
                    <select id="table_type" name="table_type" required>
                        <option value="">Select Table Type</option>
                        <option value="Window Side">Window Side (2-4 persons)</option>
                        <option value="Family">Family (4-6 persons)</option>
                        <option value="VIP">VIP (6-8 persons)</option>
                        <option value="Regular">Regular (2 persons)</option>
                        <option value="Outdoor">Outdoor (4 persons)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="table_no">Table Number *</label>
                    <select id="table_no" name="table_no" required>
                        <option value="">Select Table</option>
                        <!-- Table options from T01 to T10 -->
                        <option value="T01">T01</option>
                        <option value="T02">T02</option>
                        <option value="T03">T03</option>
                        <option value="T04">T04</option>
                        <option value="T05">T05</option>
                        <option value="T06">T06</option>
                        <option value="T07">T07</option>
                        <option value="T08">T08</option>
                        <option value="T09">T09</option>
                        <option value="T10">T10</option>
                    </select>
                </div>

                <!-- ======================
                     SECTION 4: ADDITIONAL INFORMATION
                     ====================== -->
                <div class="form-group">
                    <label for="occasion">Occasion</label>
                    <select id="occasion" name="occasion">
                        <option value="">Select Occasion</option>
                        <option value="Dinner">Dinner</option>
                        <option value="Lunch">Lunch</option>
                        <option value="Birthday">Birthday</option>
                        <option value="Anniversary">Anniversary</option>
                        <option value="Business">Business Meeting</option>
                        <option value="Family">Family Gathering</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="special_request">Special Requests</label>
                    <textarea id="special_request" name="special_request" rows="3"></textarea>
                    <!-- Textarea for multiline input -->
                </div>

                <!-- ======================
                     SECTION 5: PAYMENT DETAILS
                     ====================== -->
                <div class="form-group">
                    <label for="advance_paid">Advance Amount (PKR)</label>
                    <input type="number" id="advance_paid" name="advance_paid" 
                           min="0" step="100">
                    <!-- step="100" for increments of 100 -->
                </div>

                <div class="form-group">
                    <label for="payment_status">Payment Status</label>
                    <select id="payment_status" name="payment_status">
                        <option value="pending">Pending</option>
                        <option value="partial">Partial Payment</option>
                        <option value="paid">Fully Paid</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="booking_source">Booking Source</label>
                    <select id="booking_source" name="booking_source">
                        <option value="Website">Website</option>
                        <option value="Phone">Phone</option>
                        <option value="Walk-in">Walk-in</option>
                        <option value="Social Media">Social Media</option>
                    </select>
                </div>

                <!-- ======================
                     SUBMIT BUTTON
                     ====================== -->
                <button type="submit" class="btn-submit">Book Table Now</button>
            </form>
        </div>

        <!-- ======================
             NAVIGATION MENU
             ====================== -->
        <div class="navigation">
            <!-- Link to view reservations page -->
            <a href="view_reservations.php" class="nav-btn">📋 View All Reservations</a>
            
            <!-- Link to admin dashboard -->
            <a href="admin.php" class="nav-btn">⚙️ Admin Dashboard</a>
            
            <!-- Placeholder for contact page -->
            <a href="#" class="nav-btn">📞 Contact Us</a>
        </div>

        <!-- ======================
             FOOTER
             ====================== -->
        <div class="footer">
            <p>© 2026 Restaurant Table Reservation System</p>
            <!-- Copyright notice -->
        </div>
    </div>

    <!-- ======================
         JAVASCRIPT ENHANCEMENTS
         ====================== -->
    <script>
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Set minimum date to today for date picker
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('visit_date').min = today;
            
            // Get references to table selection elements
            const tableTypeSelect = document.getElementById('table_type');
            const tableNoSelect = document.getElementById('table_no');
            
            // Event listener for table type change
            // This could be extended with AJAX for real-time availability
            tableTypeSelect.addEventListener('change', function() {
                // In a real application, you would:
                // 1. Fetch available tables for selected type via AJAX
                // 2. Update tableNoSelect options dynamically
                
                // For now, just log the selection
                const tableType = this.value;
                console.log('Selected table type:', tableType);
            });
        });
    </script>
</body>
</html>