<?php
    session_start();

    require_once '../Database/DB.php';
    require_once '../Shared/KMKReservations_Functions.php';

    requireAdmin();

    $bookings = getFilteredBookings($conn, getBookingFilters());

    header("Content-Type: text/csv; charset=utf-8");
    header("Content-Disposition: attachment; filename=kmk_bookings_" . date("Y-m-d") . ".csv");

    $output = fopen("php://output", "w");

    fwrite($output, "\xEF\xBB\xBF");

    fputcsv($output, ["Reference", "Customer", "Email", "Phone", "Service Type", "Service", "Location",
                      "Date", "Time", "Details", "Status", "Booked On"]);

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
