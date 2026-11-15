<?php

    session_start();

    require_once '../Database/DB.php';
    require_once '../Shared/KMKReservations_Functions.php';

    requireAdmin();

    // Handle booking status updates.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $bookingId = (int) ($_POST['booking_id'] ?? 0);
        $serviceType = $_POST['service_type'] ?? '';
        $newStatus = $_POST['new_status'] ?? '';

        if (in_array($newStatus, ['Confirmed', 'Cancelled'])) {
            $updated = updateBookingStatus($conn, $serviceType, $bookingId, $newStatus);
            if ($updated !== false) {
                $_SESSION['admin_message'] = 'Booking updated to ' . $newStatus . '.';
            }
        }

        header("Location: KMKReservations_Admin.php?" . http_build_query(getBookingFilters()));
        exit;
    }

    $message = $_SESSION['admin_message'] ?? '';
    unset($_SESSION['admin_message']);

    $summary = getBookingSummary($conn);
    $userCount = getOneRow($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'user'");

    $filters  = getBookingFilters();
    $bookings = getFilteredBookings($conn, $filters);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | KMK Reservations</title>
    <link rel="stylesheet" href="KMKReservations_Admin.css?v=<?= filemtime(__DIR__ . DIRECTORY_SEPARATOR . 'KMKReservations_Admin.css') ?>">
</head>
<body>

    <header class="site nav">
        <a href="../Home/KMKReservations_Home.php" class="nav brand">
            <img src="../KMKReservations_Logo.png" alt="KMK Reservations logo">
        </a>
        <nav class="nav links">
            <a href="../Home/KMKReservations_Home.php">Home</a>
            <a href="../Restaurants/KMKReservations_Restaurants.php">Restaurant</a>
            <a href="../Accomodations/KMKReservations_Accomodations.php">Accommodations</a>
            <a href="../Drivers/KMKReservations_Drivers.php">Drivers</a>
            <a href="../MyBookings/KMKReservations_MyBookings.php">My Bookings</a>
            <a href="../MyProfile/KMKReservations_MyProfile.php">My Profile</a>
            <a class="active" href="../Admin/KMKReservations_Admin.php">Admin</a>
        </nav>
    </header>

    <main class="admin page">

        <section class="admin heading">
            <h1>Admin Dashboard</h1>
            <p>Hi, <?= htmlspecialchars($_SESSION['user_name']) ?>. Manage all KMK reservations here.</p>
        </section>

        <?php if ($message != ''): ?>
            <p class="admin message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <section class="summary">
            <div class="summary card">
                <span class="summary number"><?= (int) $summary['total'] ?></span>
                <span class="summary label">Total bookings</span>
            </div>
            <div class="summary card upcoming">
                <span class="summary number"><?= (int) $summary['upcoming'] ?></span>
                <span class="summary label">Upcoming</span>
            </div>
            <div class="summary card pending">
                <span class="summary number"><?= (int) $summary['pending'] ?></span>
                <span class="summary label">Waiting to confirm</span>
            </div>
            <div class="summary card cancelled">
                <span class="summary number"><?= (int) $summary['cancelled'] ?></span>
                <span class="summary label">Cancelled</span>
            </div>
            <div class="summary card">
                <span class="summary number"><?= (int) $userCount['total'] ?></span>
                <span class="summary label">Customers</span>
            </div>
        </section>

        <p class="per service">
            Active bookings &mdash;
            Restaurants: <strong><?= $summary['per_service']['restaurant'] ?></strong> &nbsp;|&nbsp;
            Accommodations: <strong><?= $summary['per_service']['room'] ?></strong> &nbsp;|&nbsp;
            Drivers: <strong><?= $summary['per_service']['driver'] ?></strong>
        </p>

        <!-- Booking filters -->
        <form class="filter bar" method="get" action="">
            <div class="filter field wide">
                <label for="search">Search</label>
                <input type="text" id="search" name="search" placeholder="Reference, customer, email or place"
                       value="<?= htmlspecialchars($filters['search']) ?>">
            </div>

            <div class="filter field">
                <label for="service">Service</label>
                <select id="service" name="service">
                    <option value="">All services</option>
                    <option value="restaurant" <?= $filters['service'] === 'restaurant' ? 'selected' : '' ?>>Restaurant</option>
                    <option value="room" <?= $filters['service'] === 'room' ? 'selected' : '' ?>>Accommodation</option>
                    <option value="driver" <?= $filters['service'] === 'driver' ? 'selected' : '' ?>>Driver</option>
                </select>
            </div>

            <div class="filter field">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">All statuses</option>
                    <option value="Pending" <?= $filters['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Confirmed" <?= $filters['status'] === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                    <option value="Cancelled" <?= $filters['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>

            <div class="filter field">
                <label for="from">From date</label>
                <input type="date" id="from" name="from" value="<?= htmlspecialchars($filters['from']) ?>">
            </div>

            <div class="filter field">
                <label for="to">To date</label>
                <input type="date" id="to" name="to" value="<?= htmlspecialchars($filters['to']) ?>">
            </div>

            <div class="filter buttons">
                <button type="submit" class="btn">Filter</button>
                <a href="KMKReservations_Admin.php" class="btn btn light">Clear</a>
            </div>
        </form>

        <section class="table box">
            <p class="result count"><?= count($bookings) ?> booking(s) found</p>

            <?php if (count($bookings) == 0): ?>
                <p class="no results">No bookings match your search.</p>
            <?php else: ?>
            <table class="booking table">
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
                        <td><span class="status status <?= strtolower($b['status']) ?>"><?= $b['status'] ?></span></td>
                        <td class="actions">
                            <!-- Booking status actions -->
                            <?php if ($b['status'] === 'Pending'): ?>
                                <form method="post" action="?<?= htmlspecialchars(http_build_query($filters)) ?>">
                                    <input type="hidden" name="booking_id" value="<?= (int) $b['booking_id'] ?>">
                                    <input type="hidden" name="service_type" value="<?= htmlspecialchars($b['service_type']) ?>">
                                    <input type="hidden" name="new_status" value="Confirmed">
                                    <button type="submit" class="small btn confirm">Confirm</button>
                                </form>
                            <?php endif; ?>

                            <?php if ($b['status'] !== 'Cancelled'): ?>
                                <form method="post" action="?<?= htmlspecialchars(http_build_query($filters)) ?>"
                                      onsubmit="return confirm('Cancel booking <?= htmlspecialchars($b['reference']) ?>?');">
                                    <input type="hidden" name="booking_id" value="<?= (int) $b['booking_id'] ?>">
                                    <input type="hidden" name="service_type" value="<?= htmlspecialchars($b['service_type']) ?>">
                                    <input type="hidden" name="new_status" value="Cancelled">
                                    <button type="submit" class="small btn cancel">Cancel</button>
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

    <footer class="site footer">
        <p>Plan your day your way.</p>
        <p class="footer small">&copy; 2026 KMK Reservations</p>
    </footer>
</body>
</html>
