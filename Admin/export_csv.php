<?php
    // CSV EXPORT
    // Downloads the bookings as a CSV file (opens in Excel).
    // It uses the same filters as the Admin page, so the file matches the table on screen.
    session_start();

    require_once '../Database/DB.php';
    require_once '../Shared/KMKReservations_Functions.php';

    requireAdmin();   // only admins can download

    $bookings = getFilteredBookings($conn, getBookingFilters());

    // Tell the browser this is a file to download, not a web page
    header("Content-Type: text/csv; charset=utf-8");
    header("Content-Disposition: attachment; filename=kmk_bookings_" . date("Y-m-d") . ".csv");

    // php://output means "write straight to the browser"
    $output = fopen("php://output", "w");

    fwrite($output, "\xEF\xBB\xBF");   // so Excel shows special letters properly

    // Heading row
    fputcsv($output, ["Reference", "Customer", "Email", "Phone", "Service Type", "Service", "Location",
                      "Date", "Time", "Details", "Status", "Booked On"]);

    // One row for each booking
    foreach ($bookings as $b) {
        fputcsv($output, [
            $b['reference'],
            $b['full_name'],
            $b['email'],
            $b['phone'],
            serviceName($b['service_type']),
            $b['service_name'],
            $b['location'],
            $b['booking_date'],
            $b['booking_time'] ? substr($b['booking_time'], 0, 5) : '',
            bookingDetails($b),
            $b['status'],
            $b['created_at']
        ]);
    }

    fclose($output);
    exit;
?>
