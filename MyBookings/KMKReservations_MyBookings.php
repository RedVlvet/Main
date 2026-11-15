<?php
    session_start();
    $isLoggedIn = isset($_SESSION['user_id']);

    require_once '../Database/DB.php';
    require_once '../Shared/KMKReservations_Functions.php';

    $userName = $_SESSION['user_name'] ?? 'User';

    $reservations = [
        'restaurant' => [],
        'room' => [],
        'driver' => []
    ];

    $message = '';

    if ($isLoggedIn) {

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_id'])) {
            $stmt = runQuery($conn,
                "UPDATE bookings SET status = 'Cancelled'
                 WHERE booking_id = ? AND user_id = ? AND status <> 'Cancelled'
                   AND COALESCE(end_date, booking_date) >= CURDATE()",
                "ii", [(int) $_POST['cancel_id'], $_SESSION['user_id']]);

            $message = ($stmt && mysqli_stmt_affected_rows($stmt) == 1)
                ? 'Your reservation has been cancelled.'
                : 'That reservation could not be cancelled.';
        }

        $rows = getAllRows($conn,
            "SELECT b.*, s.service_type, s.name AS service_name, s.location
             FROM bookings b
             JOIN services s ON b.service_id = s.service_id
             WHERE b.user_id = ?
             ORDER BY b.booking_date, b.booking_time",
            "i", [$_SESSION['user_id']]);

        foreach ($rows as $row) {
            $reservations[$row['service_type']][] = [
                'id'        => $row['booking_id'],
                'name'      => $row['service_name'] . ' - ' . $row['location'],
                'date'      => $row['booking_date'],
                'time'      => $row['booking_time'] ? substr($row['booking_time'], 0, 5) : 'Check-in day',
                'details'   => bookingDetails($row),
                'reference' => $row['reference'],
                'made_on'   => $row['created_at'],
                'booking_status' => $row['status'],
                'status'    => $row['status'] === 'Cancelled' ? 'previous' : bookingPeriod($row)
            ];
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings | KMK Reservations</title>
    <link rel="stylesheet" href="KMKReservations_MyBookings.css">
</head>
<body>
    <header class="site-nav">
        <a href="../Home/KMKReservations_Home.php" class="nav-brand">
            <img src="../KMKReservations_Logo.png" alt="KMK Reservations logo">
        </a>
        <nav class="nav-links">
            <a href="../Home/KMKReservations_Home.php">Home</a>
            <a href="../Restaurants/KMKReservations_Restaurants.php">Restaurant</a>
            <a href="../Accomodations/KMKReservations_Accomodations.php">Accommodations</a>
            <a href="../Drivers/KMKReservations_Drivers.php">Drivers</a>
            <a class="active" href="../MyBookings/KMKReservations_MyBookings.php">My Bookings</a>
            <a href="../MyProfile/KMKReservations_MyProfile.php">My Profile</a>
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <a href="../Admin/KMKReservations_Admin.php">Admin</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="container">
        <section class="welcome">
            <h1>Hi, <?= htmlspecialchars($userName) ?>!</h1>
            <p>These are your current and previous reservations. Where you can view in one place.</p>

            <?php if (!$isLoggedIn): ?>
                <p class="login-note">
                    Please <a href="../MyProfile/KMKReservations_MyProfile.php">log in</a> to see your reservations.
                </p>
            <?php endif; ?>

            <?php if ($message != ''): ?>
                <p class="booking-message"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>
        </section>

        <section class="service-sections">
            <button class="service-card restaurant active" data-service="restaurant">
                <h2>Restaurant</h2>
                <p>View your restaurant reservations.</p>
            </button>
            <button class="service-card room" data-service="room">
                <h2>Room</h2>
                <p>View your room reservations.</p>
            </button>
            <button class="service-card driver" data-service="driver">
                <h2>Drivers</h2>
                <p>View your driver reservations.</p>
            </button>
        </section>

        <section class="reservations">
            <h2 id="reservationTitle">Restaurant Reservations</h2>

            <div class="reservation-tabs">
                <button class="tab active" data-status="current">Current Reservations</button>
                <button class="tab" data-status="previous">Previous Reservations</button>
            </div>

            <div class="reservation-list">
                <?php foreach ($reservations as $service => $serviceReservations): ?>
                    <?php foreach ($serviceReservations as $reservation): ?>
                        <div
                            class="reservation-card"
                            data-service="<?= htmlspecialchars($service) ?>"
                            data-status="<?= htmlspecialchars($reservation['status']) ?>"
                        >
                            <h3><?= htmlspecialchars($reservation['name']) ?></h3>
                            <p><strong>Date:</strong> <?= htmlspecialchars($reservation['date']) ?></p>
                            <p><strong>Time:</strong> <?= htmlspecialchars($reservation['time']) ?></p>
                            <p><strong>Details:</strong> <?= htmlspecialchars($reservation['details']) ?></p>
                            <p><strong>Reference Number:</strong> <?= htmlspecialchars($reservation['reference']) ?></p>
                            <p><strong>Booked on:</strong> <?= htmlspecialchars($reservation['made_on']) ?></p>
                            <span class="status status-<?= strtolower($reservation['booking_status']) ?>">
                                <?= htmlspecialchars($reservation['booking_status']) ?>
                            </span>

                            <?php if ($reservation['status'] === 'current' && $reservation['booking_status'] !== 'Cancelled'): ?>
                                <form
                                    method="post"
                                    action=""
                                    class="cancel-form"
                                    onsubmit="return confirm('Cancel reservation <?= htmlspecialchars($reservation['reference']) ?>?');"
                                >
                                    <input type="hidden" name="cancel_id" value="<?= (int) $reservation['id'] ?>">
                                    <button type="submit" class="cancel-booking-btn">Cancel reservation</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>

                <div id="noReservations">
                    <h3>No Reservations Found</h3>
                    <p>You do not have any reservations in this section.</p>
                </div>
            </div>
        </section>
    </main>

    <script src="../Shared/MessagePopUps.js"></script>
    <script src="KMKReservations_MyBookings.js"></script>

    <footer class="site-footer">
        <p>Plan your day your way.</p>
        <p class="footer-small">&copy; 2026 KMK Reservations</p>
    </footer>
</body>
</html>