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
            $found = getOneRow($conn,
                "SELECT reference FROM (
                    SELECT reference FROM drivers
                    UNION ALL SELECT reference FROM restaurants
                    UNION ALL SELECT reference FROM rooms
                 ) AS reservations
                 WHERE reference = ?",
                "s", [$reference]);
        } while ($found !== null);

        return $reference;
    }

    function isValidService($serviceType, $name) {
        $options = [
            'driver' => ['Marcus', 'John', 'Phillip', 'Martin', 'Francis'],
            'restaurant' => [
                'The Jerk Centre',
                'Pier 1 Restaurant',
                'Food to go',
                "Sally's at Sunset",
                'Cabarita Inn',
                'Roots 21 | Bar & Kitchen'
            ],
            'room' => [
                'Bahia Principe Explore Jamaica',
                'The Blue House Boutique Bed & Breakfast Villa',
                'Royalton Blue Waters',
                'Golden Eye',
                'Couples Sans Souci',
                'Sandals Jamaica Montego Bay'
            ]
        ];

        return isset($options[$serviceType]) && in_array($name, $options[$serviceType], true);
    }

    function isAlreadyBooked($conn, $serviceType, $serviceName, $date, $time = null, $userId = null) {
        if ($serviceType === 'driver') {
            $sql = "SELECT driver_id FROM drivers
                    WHERE driver_name = ? AND booking_date = ? AND booking_time = ?
                      AND status <> 'Cancelled'";
            $types = "sss";
            $values = [$serviceName, $date, $time];
        } elseif ($serviceType === 'restaurant') {
            $sql = "SELECT restaurant_id FROM restaurants
                    WHERE restaurant_name = ? AND booking_date = ? AND booking_time = ?
                      AND user_id = ? AND status <> 'Cancelled'";
            $types = "sssi";
            $values = [$serviceName, $date, $time, $userId];
        } elseif ($serviceType === 'room') {
            $sql = "SELECT room_id FROM rooms
                    WHERE accommodation_name = ? AND checkin_date = ?
                      AND user_id = ? AND status <> 'Cancelled'";
            $types = "ssi";
            $values = [$serviceName, $date, $userId];
        } else {
            return false;
        }

        return getOneRow($conn, $sql, $types, $values) !== null;
    }

    function saveBooking($conn, $userId, $serviceType, $serviceName, $data) {
        $reference = makeReference($conn);

        if ($serviceType === 'driver') {
            $stmt = runQuery($conn,
                "INSERT INTO drivers
                    (reference, user_id, driver_name, booking_date, booking_time, return_date, return_time,
                     adults, children, from_place, to_place, trip_type)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                "sisssssiiiss",
                [
                    $reference,
                    $userId,
                    $serviceName,
                    $data['booking_date'],
                    $data['booking_time'],
                    $data['end_date'],
                    $data['end_time'],
                    $data['adults'],
                    $data['children'],
                    $data['from_place'],
                    $data['to_place'],
                    $data['trip_type']
                ]);
        } elseif ($serviceType === 'restaurant') {
            $stmt = runQuery($conn,
                "INSERT INTO restaurants
                    (reference, user_id, restaurant_name, booking_date, booking_time, adults, children)
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                "sisssii",
                [
                    $reference,
                    $userId,
                    $serviceName,
                    $data['booking_date'],
                    $data['booking_time'],
                    $data['adults'],
                    $data['children']
                ]);
        } elseif ($serviceType === 'room') {
            $stmt = runQuery($conn,
                "INSERT INTO rooms
                    (reference, user_id, accommodation_name, checkin_date, checkout_date,
                     adults, children, room_count)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                "sisssiii",
                [
                    $reference,
                    $userId,
                    $serviceName,
                    $data['booking_date'],
                    $data['end_date'],
                    $data['adults'],
                    $data['children'],
                    $data['rooms']
                ]);
        } else {
            return false;
        }

        return $stmt === false ? false : $reference;
    }

    function bookingQuery() {
        return "SELECT d.driver_id AS booking_id, d.reference, d.user_id, 'driver' AS service_type,
                       d.driver_name AS service_name, NULL AS location, d.booking_date, d.booking_time,
                       d.return_date AS end_date, d.return_time AS end_time, d.adults, d.children,
                       NULL AS rooms, d.from_place, d.to_place, d.trip_type, d.status, d.created_at,
                       COALESCE(u.full_name, 'Guest') AS full_name,
                       COALESCE(u.email, '') AS email, COALESCE(u.phone, '') AS phone
                FROM drivers d
                LEFT JOIN users u ON d.user_id = u.user_id
                UNION ALL
                SELECT r.restaurant_id AS booking_id, r.reference, r.user_id, 'restaurant' AS service_type,
                       r.restaurant_name AS service_name, NULL AS location, r.booking_date, r.booking_time,
                       NULL AS end_date, NULL AS end_time, r.adults, r.children, NULL AS rooms,
                       NULL AS from_place, NULL AS to_place, NULL AS trip_type, r.status, r.created_at,
                       COALESCE(u.full_name, 'Guest') AS full_name,
                       COALESCE(u.email, '') AS email, COALESCE(u.phone, '') AS phone
                FROM restaurants r
                LEFT JOIN users u ON r.user_id = u.user_id
                UNION ALL
                SELECT rm.room_id AS booking_id, rm.reference, rm.user_id, 'room' AS service_type,
                       rm.accommodation_name AS service_name, NULL AS location, rm.checkin_date AS booking_date,
                       NULL AS booking_time, rm.checkout_date AS end_date, NULL AS end_time,
                       rm.adults, rm.children, rm.room_count AS rooms, NULL AS from_place,
                       NULL AS to_place, NULL AS trip_type, rm.status, rm.created_at,
                       COALESCE(u.full_name, 'Guest') AS full_name,
                       COALESCE(u.email, '') AS email, COALESCE(u.phone, '') AS phone
                FROM rooms rm
                LEFT JOIN users u ON rm.user_id = u.user_id";
    }

    function getUserBookings($conn, $userId) {
        return getAllRows($conn,
            "SELECT * FROM (" . bookingQuery() . ") AS reservations
             WHERE user_id = ?
             ORDER BY booking_date, booking_time",
            "i", [$userId]);
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
        $sql = "SELECT * FROM (" . bookingQuery() . ") AS bookings WHERE 1 = 1";
        $types = "";
        $values = [];

        if ($filters['search'] !== '') {
            $sql .= " AND (reference LIKE ? OR full_name LIKE ? OR email LIKE ? OR service_name LIKE ?
                      OR from_place LIKE ? OR to_place LIKE ?)";
            $like = '%' . $filters['search'] . '%';
            $types .= "ssssss";
            array_push($values, $like, $like, $like, $like, $like, $like);
        }
        if (in_array($filters['service'], ['restaurant', 'room', 'driver'])) {
            $sql .= " AND service_type = ?";
            $types .= "s";
            $values[] = $filters['service'];
        }
        if (in_array($filters['status'], ['Pending', 'Confirmed', 'Cancelled'])) {
            $sql .= " AND status = ?";
            $types .= "s";
            $values[] = $filters['status'];
        }
        if (isValidDate($filters['from'])) {
            $sql .= " AND booking_date >= ?";
            $types .= "s";
            $values[] = $filters['from'];
        }
        if (isValidDate($filters['to'])) {
            $sql .= " AND booking_date <= ?";
            $types .= "s";
            $values[] = $filters['to'];
        }

        $sql .= " ORDER BY booking_date DESC, booking_time DESC";

        return getAllRows($conn, $sql, $types, $values);
    }

    function updateBookingStatus($conn, $serviceType, $bookingId, $status) {
        $tables = [
            'driver' => ['drivers', 'driver_id'],
            'restaurant' => ['restaurants', 'restaurant_id'],
            'room' => ['rooms', 'room_id']
        ];

        if (!isset($tables[$serviceType]) || !in_array($status, ['Confirmed', 'Cancelled'], true)) {
            return false;
        }

        [$table, $idColumn] = $tables[$serviceType];
        return runQuery($conn,
            "UPDATE {$table} SET status = ? WHERE {$idColumn} = ?",
            "si", [$status, $bookingId]);
    }

    function cancelUserBooking($conn, $serviceType, $bookingId, $userId) {
        $tables = [
            'driver' => ['drivers', 'driver_id', 'COALESCE(return_date, booking_date)'],
            'restaurant' => ['restaurants', 'restaurant_id', 'booking_date'],
            'room' => ['rooms', 'room_id', 'checkout_date']
        ];

        if (!isset($tables[$serviceType])) {
            return false;
        }

        [$table, $idColumn, $lastDate] = $tables[$serviceType];
        return runQuery($conn,
            "UPDATE {$table} SET status = 'Cancelled'
             WHERE {$idColumn} = ? AND user_id = ? AND status <> 'Cancelled'
               AND {$lastDate} >= CURDATE()",
            "ii", [$bookingId, $userId]);
    }

    function getBookingSummary($conn) {
        $today = date('Y-m-d');
        $rows = getAllRows($conn,
            "SELECT service_type, COUNT(*) AS total,
                    SUM(status <> 'Cancelled') AS active,
                    SUM(status = 'Pending') AS pending,
                    SUM(status = 'Confirmed') AS confirmed,
                    SUM(status = 'Cancelled') AS cancelled,
                    SUM(status <> 'Cancelled' AND COALESCE(end_date, booking_date) >= ?) AS upcoming
             FROM (" . bookingQuery() . ") AS reservations
             GROUP BY service_type",
            "s", [$today]);

        $summary = [
            'total' => 0,
            'pending' => 0,
            'confirmed' => 0,
            'cancelled' => 0,
            'upcoming' => 0,
            'per_service' => ['restaurant' => 0, 'room' => 0, 'driver' => 0]
        ];

        foreach ($rows as $row) {
            $summary['per_service'][$row['service_type']] = (int) $row['active'];
            foreach (['total', 'pending', 'confirmed', 'cancelled', 'upcoming'] as $key) {
                $summary[$key] += (int) $row[$key];
            }
        }

        return $summary;
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
