<?php
    // Start the session and checks the user is logged in.
    session_start();
    $isLoggedIn = isset($_SESSION['user_id']);

    // Database connection and shared functions
    require_once '../Database/DB.php';
    require_once '../Shared/KMKReservations_Functions.php';
    $basepath = dirname(__DIR__);
    $base = str_replace('\\','/',$basepath);

    //Uses the name of the user currently logged in
    $userName = $_SESSION['user_name'] ?? 'User';

    // An array that stores all reservation made
    $reservations =
    ["restaurant" => [],
    "room" => [],
    "driver" => []
    ];

    $message = '';

    if ($isLoggedIn) {

        // Cancel a booking (the booking is not deleted, its status becomes "Cancelled")
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_id'])) {
            // "AND user_id = ?" makes sure users can only cancel their OWN bookings
            // and only upcoming ones (a booking in the past can't be cancelled)
            $stmt = runQuery($conn,
                "UPDATE bookings SET status = 'Cancelled'
                 WHERE booking_id = ? AND user_id = ? AND status <> 'Cancelled'
                   AND COALESCE(end_date, booking_date) >= CURDATE()",
                "ii", [(int) $_POST['cancel_id'], $_SESSION['user_id']]);

            $message = ($stmt && mysqli_stmt_affected_rows($stmt) == 1)
                ? 'Your reservation has been cancelled.'
                : 'That reservation could not be cancelled.';
        }

        //retrieves records belonging to a user
        $rows = getAllRows($conn,
            "SELECT b.*, s.service_type, s.name AS service_name, s.location
             FROM bookings b
             JOIN services s ON b.service_id = s.service_id
             WHERE b.user_id = ?
             ORDER BY b.booking_date, b.booking_time",
            "i", [$_SESSION['user_id']]);

        // Put each booking in the right group (restaurant, room or driver)
        foreach ($rows as $row) {
            $reservations[$row['service_type']][] = [
                'id'        => $row['booking_id'],
                'name'      => $row['service_name'] . ' - ' . $row['location'],
                'date'      => $row['booking_date'],
                'time'      => $row['booking_time'] ? substr($row['booking_time'], 0, 5) : 'Check-in day',
                'details'   => bookingDetails($row),
                'reference' => $row['reference'],
                'made_on'   => $row['created_at'],          // the date the reservation was made
                'booking_status' => $row['status'],         // Pending, Confirmed or Cancelled
                // Cancelled bookings go under "Previous"
                'status'    => $row['status'] === 'Cancelled' ? 'previous' : bookingPeriod($row)
            ];
        }
    }
?>

<!DOCTYPE html>
<html lang = "en">
<head>
    <meta charset = "UTF-8">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
    <title> My Bookings | KMK Reservations</title>
    <link rel = "stylesheet" href = "KMKReservations_MyBookings.css">    
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
                <a class="active" href="../MyBookings/KMKReservations_MyBookings.php">My Bookings</a>
                <a href="../MyProfile/KMKReservations_MyProfile.php">My Profile</a>
                <!-- Admin link: only admins see it -->
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a href="../Admin/KMKReservations_Admin.php">Admin</a>
                <?php endif; ?>
            </nav>
        </header>
        
        <main class = "container">

            <!--Welcome Message -->
            <section class = "welcome">
                <h1> Hi, <?php echo htmlspecialchars($userName); ?>! </h1>
                <p>
                    These are your current and previous reservations.
                    Where you can view in one place.
                </p>

                <!-- Shown when nobody is logged in -->
                <?php if (!$isLoggedIn): ?>
                    <p class="login-note">
                        Please <a href="../MyProfile/KMKReservations_MyProfile.php">log in</a> to see your reservations.
                    </p>
                <?php endif; ?>

                <!-- Message after cancelling -->
                <?php if ($message != ''): ?>
                    <p class="booking-message"><?php echo htmlspecialchars($message); ?></p>
                <?php endif; ?>
            </section>

            <!-- Reservation Service Section -->
            <section class = "service-sections">
                <button class = "service-card restaurant active" data-service = "restaurant">
                    <h2> Restaurant </h2>
                    <p> View your restaurant reservations.</p>
                </button>

                <button class = "service-card room" data-service = "room">
                    <h2> Room </h2>
                    <p> View your room reservations. </p>
                </button>

                <button class = "service-card driver" data-service = "driver">
                    <h2> Drivers </h2>
                    <p> View your driver reservations. </p>
                </button>
            </section>

            <!-- Reservation Information -->
            <section class = "reservations">
                <h2 id = "reservationTitle">
                    Restaurant Reservations
                </h2>

                <!-- Current and Previous Tabs -->
                <div class = "reservation-tabs">
                    <button class = "tab active" data-status = "current">
                        Current Reservations
                    </button>

                    <button class = "tab" data-status = "previous">
                        Previous Reservations
                    </button>
                </div> 

                <!-- Reservation Cards -->
                <div class = "reservation-list">
                    <!-- Loop through each service, then each reservation in that service -->
                    <?php foreach ($reservations as $service => $serviceReservations): ?>
                    <?php foreach ($serviceReservations as $reservation): ?>
                        <div class = "reservation-card"
                        data-service = "<?php echo htmlspecialchars($service); ?>"
                        data-status = "<?php echo htmlspecialchars($reservation['status']); ?>">

                        <h3> 
                            <?php echo htmlspecialchars($reservation['name']); ?>
                        </h3>

                        <p> 
                            <strong> Date: </strong>
                            <?php echo htmlspecialchars($reservation['date']); ?>
                        </p>

                        <p> 
                            <strong> Time: </strong>
                            <?php echo htmlspecialchars($reservation['time']); ?>
                        </p>

                        <p> 
                            <strong> Details: </strong>
                            <?php echo htmlspecialchars($reservation['details']); ?>
                        </p>

                        <p> 
                            <strong> Reference Number: </strong>
                            <?php echo htmlspecialchars($reservation['reference']); ?>
                        </p>

                        <p> 
                            <strong> Booked on: </strong>
                            <?php echo htmlspecialchars($reservation['made_on']); ?>
                        </p>
                        
                        <!-- Display the reservation status (Pending, Confirmed or Cancelled) -->
                        <span class = "status status-<?php echo strtolower($reservation['booking_status']); ?>"> 
                            <?php echo htmlspecialchars($reservation['booking_status']); ?>
                        </span>

                        <!-- Cancel button (only for current bookings that are not cancelled) -->
                        <?php if ($reservation['status'] === 'current' && $reservation['booking_status'] !== 'Cancelled'): ?>
                            <form method="post" action="" class="cancel-form"
                                  onsubmit="return confirm('Cancel reservation <?php echo htmlspecialchars($reservation['reference']); ?>?');">
                                <input type="hidden" name="cancel_id" value="<?php echo (int) $reservation['id']; ?>">
                                <button type="submit" class="cancel-booking-btn">Cancel reservation</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- End the foreach loops --> 
                    <?php endforeach; ?>
                    <?php endforeach; ?>

                    <!-- Message displaying that there are no reservations --> 
                    <div id = "noReservations"> 
                        <h3> No Reservations Found </h3>
                        <p> 
                            You do not have any reservations in this section.
                        </p>
                    </div>
                </div>           
            </section>
        </main>

        <!-- Link the JavaScript file -->
        <script src="../Shared/MessagePopUps.js"></script>
        <script src="KMKReservations_MyBookings.js"></script>         

       <!-- footer with copyright and slogan-->    
        <footer class="site-footer">
            <p>Plan your day your way.</p>
            <p class="footer-small">&copy; 2026 KMK Reservations</p>
        </footer>
    </body>
</html>