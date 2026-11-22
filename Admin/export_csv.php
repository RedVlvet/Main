<?php
    session_start();

    require_once '../Database/DB.php';
    require_once '../Shared/KMKReservations_Functions.php';

    requireAdmin();

    $bookings = getFilteredBookings($conn, getBookingFilters());

    $csvPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'kmk_bookings.csv';
    $output = fopen($csvPath, 'c+b');
    if ($output === false) {
        http_response_code(500);
        exit('Unable to update the bookings CSV.');
    }

    if (!flock($output, LOCK_EX)) {
        fclose($output);
        http_response_code(500);
        exit('Unable to update the bookings CSV.');
    }

    $writeSucceeded = ftruncate($output, 0) && rewind($output);
    $writeSucceeded = $writeSucceeded && fwrite($output, "\xEF\xBB\xBF") === 3;
    $writeSucceeded = $writeSucceeded && fputcsv($output, [
        "Reference", "Customer", "Email", "Phone", "Service Type", "Service", "Location",
        "Date", "Time", "Details", "Status", "Booked On"
    ]) !== false;

    foreach ($bookings as $b) {
        if (!$writeSucceeded) {
            break;
        }

        $writeSucceeded = fputcsv($output, [
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
        ]) !== false;
    }

    $writeSucceeded = $writeSucceeded && fflush($output) && rewind($output);
    if (!$writeSucceeded) {
        flock($output, LOCK_UN);
        fclose($output);
        http_response_code(500);
        exit('Unable to update the bookings CSV.');
    }

    
    header("Content-Type: text/csv; charset=utf-8");
    header("Content-Disposition: attachment; filename=kmk_bookings.csv");
    if (fpassthru($output) === false) {
        error_log('Unable to stream the KMK bookings CSV.');
    }
    flock($output, LOCK_UN);
    fclose($output);
    exit;
?>
