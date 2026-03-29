<?php
include 'credential.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1️⃣ Form data receive
    $customer_name = $_POST['customer_name'];
    $phone = $_POST['phone'];
    $visit_date = $_POST['visit_date'];
    $visit_time = $_POST['visit_time'];
    $table_no = $_POST['table_no'];
    $number_of_persons = $_POST['number_of_persons'];
    $table_type = $_POST['table_type'];
    $payment_status = $_POST['payment_status'];
    $status = 'pending';

    // 2️⃣ DUPLICATE CHECK (YE MAIN FIX HAI)
    $checkSql = "SELECT reservation_id FROM reservations
                 WHERE visit_date = ? AND visit_time = ? AND table_no = ?";
    $stmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($stmt, "ssi", $visit_date, $visit_time, $table_no);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "<h2 style='color:red; text-align:center;'> Table already booked for this time!</h2>";
        echo "<p style='text-align:center;'><a href='index.php'>Go Back</a></p>";
        exit;
    }
    mysqli_stmt_close($stmt);

    // 3️⃣ INSERT QUERY (SAFE NOW)
    $insertSql = "INSERT INTO reservations
    (customer_name, phone, visit_date, visit_time, table_no, number_of_persons, table_type, payment_status, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $insertSql);
    mysqli_stmt_bind_param(
        $stmt,
        "ssssissss",
        $customer_name,
        $phone,
        $visit_date,
        $visit_time,
        $table_no,
        $number_of_persons,
        $table_type,
        $payment_status,
        $status
    );
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // 4️⃣ REDIRECT (Double submit problem khatam)
    header("Location: view_reservations.php?success=1");
    exit;
}
?>
