<?php
    // DATABASE CONNECTION
    // Every page that needs the database loads this file at the top with:
    //     require_once '../Database/DB.php';

    // Turn off PHP's own big error page so our "=== false" check below works
    mysqli_report(MYSQLI_REPORT_OFF);

    // localhost, root and no password are the normal XAMPP settings
    $conn = mysqli_connect("localhost", "root", "", "kmk_reservations");

    if ($conn === false) {
        // The real reason goes in the XAMPP error log, the user only sees a simple message
        error_log("KMK database connection failed: " . mysqli_connect_error());
        die("Sorry, the website cannot connect to the database right now. Make sure MySQL is running in XAMPP and kmk_reservations.sql was imported.");
    }

    // So names with accents show properly
    mysqli_set_charset($conn, "utf8mb4");
?>
