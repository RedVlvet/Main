<?php
    mysqli_report(MYSQLI_REPORT_OFF);

    // Connect to the reservations database.
    $conn = mysqli_connect("localhost", "root", "", "kmk_reservations");

    if ($conn === false) {
        error_log("KMK database connection failed: " . mysqli_connect_error());
        die("Sorry, the website cannot connect to the database right now. Make sure MySQL is running in XAMPP and kmk_reservations.sql was imported.");
    }

    mysqli_set_charset($conn, "utf8mb4");
?>
