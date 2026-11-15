<?php
    function runQuery($conn, $sql, $types = "", $values = []) {
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt === false) {
            error_log("Query could not be prepared: " . mysqli_error($conn));
            return false;
        }

        if ($types !== "") {
            mysqli_stmt_bind_param($stmt, $types, ...$values);
        }

        if (!mysqli_stmt_execute($stmt)) {
            error_log("Query failed: " . mysqli_stmt_error($stmt));
            return false;
        }

        return $stmt;
    }

    function getOneRow($conn, $sql, $types = "", $values = []) {
        $stmt = runQuery($conn, $sql, $types, $values);
        if ($stmt === false) {
            return null;
        }
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        return $row ?: null;
    }

    function getAllRows($conn, $sql, $types = "", $values = []) {
        $stmt = runQuery($conn, $sql, $types, $values);
        if ($stmt === false) {
            return [];
        }
        return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    }

    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    function isAdmin() {
        return isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin';
    }

    function requireAdmin() {
        if (!isLoggedIn()) {
            header("Location: ../MyProfile/KMKReservations_MyProfile.php");
            exit;
        }
        if (!isAdmin()) {
            header("Location: ../Home/KMKReservations_Home.php");
            exit;
        }
    }

    function makeReference($conn) {
        do {
            $reference = 'KMK-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
            $found = getOneRow($conn, "SELECT booking_id FROM bookings WHERE reference = ?", "s", [$reference]);
        } while ($found !== null);

        return $reference;
    }

    function getServiceId($conn, $serviceType, $name) {
        $row = getOneRow($conn, "SELECT service_id FROM services WHERE service_type = ? AND name = ?",
                         "ss", [$serviceType, $name]);
        return $row ? $row['service_id'] : null;
    }

    function isAlreadyBooked($conn, $serviceId, $date, $time, $userId = null) {
        $sql = "SELECT booking_id FROM bookings
                WHERE service_id = ? AND booking_date = ? AND booking_time <=> ? AND status <> 'Cancelled'";
        $types = "iss";
        $values = [$serviceId, $date, $time];

        if ($userId !== null) {
            $sql .= " AND user_id = ?";
            $types .= "i";
            $values[] = $userId;
        }

        return getOneRow($conn, $sql, $types, $values) !== null;
    }

    function saveBooking($conn, $userId, $serviceId, $data) {
        $reference = makeReference($conn);

        $stmt = runQuery($conn,
            "INSERT INTO bookings (reference, user_id, service_id, booking_date, booking_time, end_date, end_time,
                                   adults, children, rooms, from_place, to_place, trip_type)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            "siissssiiisss",
            [
                $reference,
                $userId,
                $serviceId,
                $data['booking_date'],
                $data['booking_time'] ?? null,
                $data['end_date'] ?? null,
                $data['end_time'] ?? null,
                $data['adults'],
                $data['children'],
                $data['rooms'] ?? null,
                $data['from_place'] ?? null,
                $data['to_place'] ?? null,
                $data['trip_type'] ?? null
            ]);

        return $stmt === false ? false : $reference;
    }

    function bookingPeriod($booking) {
        $lastDay = $booking['end_date'] ?? $booking['booking_date'];
        if ($lastDay == '') {
            $lastDay = $booking['booking_date'];
        }
        return $lastDay >= date('Y-m-d') ? 'current' : 'previous';
    }

    function bookingDetails($booking) {
        $people = $booking['adults'] . ' adult(s), ' . $booking['children'] . ' child(ren)';

        if ($booking['service_type'] === 'room') {
            return 'Check-out ' . $booking['end_date'] . ', ' . $booking['rooms'] . ' room(s), ' . $people;
        }

        if ($booking['service_type'] === 'driver') {
            $text = $booking['from_place'] . ' to ' . $booking['to_place'] . ', ' . ucfirst($booking['trip_type']) . ' trip';
            if ($booking['trip_type'] === 'return') {
                $text .= ' (back ' . $booking['end_date'] . ' ' . substr($booking['end_time'], 0, 5) . ')';
            }
            return $text . ', ' . $people;
        }

        return $people;
    }

    function getBookingFilters() {
        return [
            'search'  => trim($_GET['search'] ?? ''),
            'service' => $_GET['service'] ?? '',
            'status'  => $_GET['status'] ?? '',
            'from'    => $_GET['from'] ?? '',
            'to'      => $_GET['to'] ?? ''
        ];
    }

    function getFilteredBookings($conn, $filters) {
        $sql = "SELECT b.*, s.service_type, s.name AS service_name, s.location,
                       u.full_name, u.email, u.phone
                FROM bookings b
                JOIN services s ON b.service_id = s.service_id
                JOIN users u    ON b.user_id = u.user_id
                WHERE 1 = 1";
        $types = "";
        $values = [];

        if ($filters['search'] !== '') {
            $sql .= " AND (b.reference LIKE ? OR u.full_name LIKE ? OR u.email LIKE ? OR s.name LIKE ?)";
            $like = '%' . $filters['search'] . '%';
            $types .= "ssss";
            array_push($values, $like, $like, $like, $like);
        }
        if (in_array($filters['service'], ['restaurant', 'room', 'driver'])) {
            $sql .= " AND s.service_type = ?";
            $types .= "s";
            $values[] = $filters['service'];
        }
        if (in_array($filters['status'], ['Pending', 'Confirmed', 'Cancelled'])) {
            $sql .= " AND b.status = ?";
            $types .= "s";
            $values[] = $filters['status'];
        }
        if (isValidDate($filters['from'])) {
            $sql .= " AND b.booking_date >= ?";
            $types .= "s";
            $values[] = $filters['from'];
        }
        if (isValidDate($filters['to'])) {
            $sql .= " AND b.booking_date <= ?";
            $types .= "s";
            $values[] = $filters['to'];
        }

        $sql .= " ORDER BY b.booking_date DESC, b.booking_time DESC";

        return getAllRows($conn, $sql, $types, $values);
    }

    function isValidDate($date) {
        $check = DateTime::createFromFormat('Y-m-d', $date);
        return $check && $check->format('Y-m-d') === $date;
    }

    function serviceName($type) {
        $names = ['restaurant' => 'Restaurant', 'room' => 'Accommodation', 'driver' => 'Driver'];
        return $names[$type] ?? $type;
    }

    function isValidPhone($phone) {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        return strlen($digits) >= 7 && strlen($digits) <= 15;
    }

    function checkPassword($password, $confirm, &$errors) {
        if (strlen($password) < 8) {
            $errors[] = 'The password must be at least 8 characters.';
        }
        if ($password !== $confirm) {
            $errors[] = 'The passwords do not match.';
        }
    }
?>
