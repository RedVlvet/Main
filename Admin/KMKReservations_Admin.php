<?php
    // ADMIN PAGE
    // Only admins can open this page. It shows every booking, a summary,
    // a search / filter, and lets the admin confirm or cancel bookings.
    session_start();

    // Database connection and shared functions
    require_once '../Database/DB.php';
    require_once '../Shared/KMKReservations_Functions.php';

    // The role is checked in PHP (hiding the link is not enough)
    requireAdmin();

    // ---------- 1. Confirm or cancel a booking ----------
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $bookingId = (int) ($_POST['booking_id'] ?? 0);
        $newStatus = $_POST['new_status'] ?? '';

        // Only these two changes are allowed
        if (in_array($newStatus, ['Confirmed', 'Cancelled'])) {
            runQuery($conn, "UPDATE bookings SET status = ? WHERE booking_id = ?", "si", [$newStatus, $bookingId]);
            $_SESSION['admin_message'] = 'Booking updated to ' . $newStatus . '.';
        }

        // Go back to the same page with the same filters (so refreshing doesn't send the form again)
        header("Location: KMKReservations_Admin.php?" . http_build_query(getBookingFilters()));
        exit;
    }

    // Message from the last confirm / cancel (shown once)
    $message = $_SESSION['admin_message'] ?? '';
    unset($_SESSION['admin_message']);

    // ---------- 2. Summary numbers ----------
    $today = date('Y-m-d');
    $summary = getOneRow($conn,
        "SELECT COUNT(*) AS total,
                SUM(status = 'Pending')   AS pending,
                SUM(status = 'Confirmed') AS confirmed,
                SUM(status = 'Cancelled') AS cancelled,
                SUM(status <> 'Cancelled' AND COALESCE(end_date, booking_date) >= ?) AS upcoming
         FROM bookings", "s", [$today]);

    $userCount = getOneRow($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'user'");

    // Bookings for each service
    $perService = ['restaurant' => 0, 'room' => 0, 'driver' => 0];
    $serviceRows = getAllRows($conn,
        "SELECT s.service_type, COUNT(*) AS total
         FROM bookings b JOIN services s ON b.service_id = s.service_id
         WHERE b.status <> 'Cancelled'
         GROUP BY s.service_type");
    foreach ($serviceRows as $row) {
        $perService[$row['service_type']] = $row['total'];
    }

    // ---------- 3. Search / filter ----------
    $filters  = getBookingFilters();
    $bookings = getFilteredBookings($conn, $filters);

    // Link for the CSV download, with the same filters as the table
    $csvLink = 'export_csv.php?' . http_build_query($filters);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | KMK Reservations</title>
    <link rel="stylesheet" href="KMKReservations_Admin.css">
</head>
<body>

    <!-- Navigation Bar -->
    <header class="site-nav">
        <a href="../Home/KMKReservations_Home.php" class="nav-brand">
            <img src="../KMKReservations_Logo.png" alt="KMK Reservations logo">
        </a>
        <nav class="nav-links">
            <a href="../Home/KMKReservations_Home.php">Home</a>
            <a href="../Restaurants/KMKReservations_Restaurants.php">Restaurant</a>
            <a href="../Accomodations/KMKReservations_Accomodations.php">Accommodations</a>
            <a href="../Drivers/KMKReservations_Drivers.php">Drivers</a>
            <a href="../MyBookings/KMKReservations_MyBookings.php">My Bookings</a>
            <a href="../MyProfile/KMKReservations_MyProfile.php">My Profile</a>
            <a class="active" href="../Admin/KMKReservations_Admin.php">Admin</a>
        </nav>
    </header>

    <main class="admin-page">

        <!-- Heading -->
        <section class="admin-heading">
            <h1>Admin Dashboard</h1>
            <p>Hi, <?= htmlspecialchars($_SESSION['user_name']) ?>. Manage all KMK reservations here.</p>
        </section>

        <?php if ($message != ''): ?>
            <p class="admin-message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <!-- Summary cards -->
        <section class="summary">
            <div class="summary-card">
                <span class="summary-number"><?= (int) $summary['total'] ?></span>
                <span class="summary-label">Total bookings</span>
            </div>
            <div class="summary-card upcoming">
                <span class="summary-number"><?= (int) $summary['upcoming'] ?></span>
                <span class="summary-label">Upcoming</span>
            </div>
            <div class="summary-card pending">
                <span class="summary-number"><?= (int) $summary['pending'] ?></span>
                <span class="summary-label">Waiting to confirm</span>
            </div>
            <div class="summary-card cancelled">
                <span class="summary-number"><?= (int) $summary['cancelled'] ?></span>
                <span class="summary-label">Cancelled</span>
            </div>
            <div class="summary-card">
                <span class="summary-number"><?= (int) $userCount['total'] ?></span>
                <span class="summary-label">Customers</span>
            </div>
        </section>

        <p class="per-service">
            Active bookings &mdash;
            Restaurants: <strong><?= $perService['restaurant'] ?></strong> &nbsp;|&nbsp;
            Accommodations: <strong><?= $perService['room'] ?></strong> &nbsp;|&nbsp;
            Drivers: <strong><?= $perService['driver'] ?></strong>
        </p>

        <!-- Search and filter (GET, so the filters show in the address bar) -->
        <form class="filter-bar" method="get" action="">
            <div class="filter-field wide">
                <label for="search">Search</label>
                <input type="text" id="search" name="search" placeholder="Reference, customer, email or place"
                       value="<?= htmlspecialchars($filters['search']) ?>">
            </div>

            <div class="filter-field">
                <label for="service">Service</label>
                <select id="service" name="service">
                    <option value="">All services</option>
                    <option value="restaurant" <?= $filters['service'] === 'restaurant' ? 'selected' : '' ?>>Restaurant</option>
                    <option value="room" <?= $filters['service'] === 'room' ? 'selected' : '' ?>>Accommodation</option>
                    <option value="driver" <?= $filters['service'] === 'driver' ? 'selected' : '' ?>>Driver</option>
                </select>
            </div>

            <div class="filter-field">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">All statuses</option>
                    <option value="Pending" <?= $filters['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Confirmed" <?= $filters['status'] === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                    <option value="Cancelled" <?= $filters['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>

            <div class="filter-field">
                <label for="from">From date</label>
                <input type="date" id="from" name="from" value="<?= htmlspecialchars($filters['from']) ?>">
            </div>

            <div class="filter-field">
                <label for="to">To date</label>
                <input type="date" id="to" name="to" value="<?= htmlspecialchars($filters['to']) ?>">
            </div>

            <div class="filter-buttons">
                <button type="submit" class="btn">Filter</button>
                <a href="KMKReservations_Admin.php" class="btn btn-light">Clear</a>
                <a href="<?= htmlspecialchars($csvLink) ?>" class="btn btn-csv">Export CSV</a>
            </div>
        </form>

        <!-- All bookings -->
        <section class="table-box">
            <p class="result-count"><?= count($bookings) ?> booking(s) found</p>

            <?php if (count($bookings) == 0): ?>
                <p class="no-results">No bookings match your search.</p>
            <?php else: ?>
            <table class="booking-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Details</th>
                        <th>Booked on</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['reference']) ?></td>
                        <td>
                            <?= htmlspecialchars($b['full_name']) ?><br>
                            <small><?= htmlspecialchars($b['email']) ?></small>
                        </td>
                        <td>
                            <?= htmlspecialchars($b['service_name']) ?><br>
                            <small><?= serviceName($b['service_type']) ?></small>
                        </td>
                        <td>
                            <?= htmlspecialchars($b['booking_date']) ?>
                            <?php if ($b['booking_time']): ?><br><small><?= substr($b['booking_time'], 0, 5) ?></small><?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars(bookingDetails($b)) ?></td>
                        <td><small><?= htmlspecialchars($b['created_at']) ?></small></td>
                        <td><span class="status status-<?= strtolower($b['status']) ?>"><?= $b['status'] ?></span></td>
                        <td class="actions">
                            <?php if ($b['status'] === 'Pending'): ?>
                                <form method="post" action="?<?= htmlspecialchars(http_build_query($filters)) ?>">
                                    <input type="hidden" name="booking_id" value="<?= (int) $b['booking_id'] ?>">
                                    <input type="hidden" name="new_status" value="Confirmed">
                                    <button type="submit" class="small-btn confirm">Confirm</button>
                                </form>
                            <?php endif; ?>

                            <?php if ($b['status'] !== 'Cancelled'): ?>
                                <form method="post" action="?<?= htmlspecialchars(http_build_query($filters)) ?>"
                                      onsubmit="return confirm('Cancel booking <?= htmlspecialchars($b['reference']) ?>?');">
                                    <input type="hidden" name="booking_id" value="<?= (int) $b['booking_id'] ?>">
                                    <input type="hidden" name="new_status" value="Cancelled">
                                    <button type="submit" class="small-btn cancel">Cancel</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </section>
    </main>

    <!-- footer with copyright and slogan-->
    <footer class="site-footer">
        <p>Plan your day your way.</p>
        <p class="footer-small">&copy; 2026 KMK Reservations</p>
    </footer>
</body>
</html>
