<?php
    session_start();
    $isLoggedIn = isset($_SESSION['user_id']);

    require_once '../Database/DB.php';
    require_once '../Shared/KMKReservations_Functions.php';

    $name = $_SESSION['user_name'] ?? '';
    $restaurant = '';
    $date = $time = '';
    $adults = '1';
    $children = '0';
    $error = '';
    $done  = false;

    // Validate submitted restaurant details.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $restaurant = trim($_POST['restaurant']);
        $date       = $_POST['date'];
        $time       = $_POST['time'];
        $adults     = $_POST['adults'] ?? '';
        $children   = $_POST['children'] ?? '0';

        if (!$isLoggedIn) {
            $error = 'Please log in or create an account before making a reservation.';
        } elseif ($restaurant == '' || $date == '' || $time == '' || $adults == '') {
            $error = 'All fields are required.';
        } elseif ($date < date('Y-m-d')) {
            $error = 'The date cannot be in the past.';
        } elseif ($adults < 1 || $adults > 6 || $children < 0 || $children > 4) {
            $error = 'Please choose a valid number of guests.';
        } elseif (!isValidService('restaurant', $restaurant)) {
            $error = 'Please choose a restaurant from the list.';
        } elseif (isAlreadyBooked($conn, 'restaurant', $restaurant, $date, $time, $_SESSION['user_id'])) {
            $error = 'You already have a reservation here at that date and time.';
        } else {
            $reference = saveBooking($conn, $_SESSION['user_id'], 'restaurant', $restaurant, [
                'booking_date' => $date,
                'booking_time' => $time,
                'adults'       => (int) $adults,
                'children'     => (int) $children
            ]);

            if ($reference === false) {
                $error = 'Sorry, your reservation could not be saved. Please try again.';
            } else {
                $done = true;
                $restaurant = $date = $time = '';
                $adults = '1';
                $children = '0';
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Restaurants | KMK Reservations</title>
        <link rel="stylesheet" href="KMKReservations_Restaurants.css?v=<?= filemtime(__DIR__ . DIRECTORY_SEPARATOR . 'KMKReservations_Restaurants.css') ?>">
        <link rel="stylesheet" href="../Shared/MessagePopUps.css?v=<?= filemtime(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Shared' . DIRECTORY_SEPARATOR . 'MessagePopUps.css') ?>">
    </head>

    <body data-done="<?php echo $done ? '1' : '0'; ?>">
        <header class="site nav">
            <a href="../Home/KMKReservations_Home.php" class="nav brand">
                <img src="../KMKReservations_Logo.png" alt="KMK Reservations logo">
            </a>
            <nav class="nav links">
                <a href="../Home/KMKReservations_Home.php">Home</a>
                <a class="active" href="../Restaurants/KMKReservations_Restaurants.php">Restaurant</a>
                <a href="../Accomodations/KMKReservations_Accomodations.php">Accommodations</a>
                <a href="../Drivers/KMKReservations_Drivers.php">Drivers</a>
                <a href="../MyBookings/KMKReservations_MyBookings.php">My Bookings</a>
                <a href="../MyProfile/KMKReservations_MyProfile.php">My Profile</a>
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a href="../Admin/KMKReservations_Admin.php">Admin</a>
                <?php endif; ?>
            </nav>
        </header>
        
        <main class="restaurants hero">
            <section class="hero content">
                <h1>Restaurant Reservations</h1>
                <p>Find and reserve a table at your favourite restaurant.</p>
            </section>
            <section class="reserve panel">
                <h2> Reserve your table </h2>

                <?php if ($error != ''): ?>
                    <p class="alert error"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <!-- Reservation inputs -->
                <form
                    id="bookingForm"
                    action=""
                    method="POST"
                    data-service="restaurants"
                    data-logged-in="<?php echo $isLoggedIn ? 'true' : 'false'; ?>"
                    data-profile-url="../MyProfile/KMKReservations_MyProfile.php"
                >

                    <input type="hidden" name="service" value="restaurants">

                    <div class="form group">
                        <label for="restaurant">Select restaurant:</label>
                        <select id="restaurant" name="restaurant" required>
                            <option value="">Select a restaurant</option>
                            <option value="The Jerk Centre" <?= $restaurant === 'The Jerk Centre' ? 'selected' : '' ?>>The Jerk Centre - Ocho Rios</option>
                            <option value="Pier 1 Restaurant" <?= $restaurant === 'Pier 1 Restaurant' ? 'selected' : '' ?>>Pier 1 Restaurant -Howard Cooke Boulevard</option>
                            <option value="Food to go" <?= $restaurant === 'Food to go' ? 'selected' : '' ?>>Food to go - Ocho Rios</option>
                            <option value="Sally's at Sunset" <?= $restaurant === "Sally's at Sunset" ? 'selected' : '' ?>>Sally's at Sunset - Negril</option>
                            <option value="Cabarita Inn" <?= $restaurant === 'Cabarita Inn' ? 'selected' : '' ?>>Cabarita Inn - Port Maria</option>
                            <option value="Roots 21 | Bar &amp; Kitchen" <?= $restaurant === 'Roots 21 | Bar & Kitchen' ? 'selected' : '' ?>>Roots 21 | Bar &amp; Kitchen - Port Antonio</option>
                        </select>
                    </div>

                    <div class="form group">
                        <label for="date"> Reservation Date: </label>
                        <input type="date" id="date" name="date" value="<?= htmlspecialchars($date) ?>" required>
                    </div>

                    <div class="form group">
                        <label for="time">Time for your reservation</label>
                        <input type="time" id="time" name="time" value="<?= htmlspecialchars($time) ?>" required>
                    </div>
                   
                    <div class="form group">
                        <label for="adults">Adults:</label>
                        <select id="adults" name="adults" required>
                            <option value="1" <?= $adults == '1' ? 'selected' : '' ?>>1 Adult</option>
                            <option value="2" <?= $adults == '2' ? 'selected' : '' ?>>2 Adults</option>
                            <option value="3" <?= $adults == '3' ? 'selected' : '' ?>>3 Adults</option>
                            <option value="4" <?= $adults == '4' ? 'selected' : '' ?>>4 Adults</option>
                            <option value="5" <?= $adults == '5' ? 'selected' : '' ?>>5 Adults</option>
                            <option value="6" <?= $adults == '6' ? 'selected' : '' ?>>6 Adults</option>
                        </select>
                    </div>

                    <div class="form group">
                        <label for="children">Children:</label>
                        <select id="children" name="children">
                            <option value="0" <?= $children == '0' ? 'selected' : '' ?>>No Children</option>
                            <option value="1" <?= $children == '1' ? 'selected' : '' ?>>1 Child</option>
                            <option value="2" <?= $children == '2' ? 'selected' : '' ?>>2 Children</option>
                            <option value="3" <?= $children == '3' ? 'selected' : '' ?>>3 Children</option>
                            <option value="4" <?= $children == '4' ? 'selected' : '' ?>>4 Children</option>
                        </select>
                    </div>
                    <button type="submit" class="reserve btn">Check Availability</button>
                </form>
            </section>
        </main>
        

        <div id="bookingModal" class="modal overlay">
            <div class="modal box">
                <button
                    type="button"
                    id="modalClose"
                    class="close btn"
                    aria-label="Close pop-up">
                    &times;
                </button>

                <div id="modalIcon" class="modal icon">!</div>
                <h2 id="modalTitle" class="modal title">Message Title</h2>
                <p id="modalMessage" class="modal message">Message will appear here.</p>

                <div class="modal actions">
                    <button
                        type="button"
                        id="modalPrimary"
                        class="modal button">
                        Continue
                    </button>
                    <button
                        type="button"
                        id="modalSecondary"
                        class="modal button secondary">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <script src="../Shared/MessagePopUps.js"></script>
        <script src="KMKReservations_Restaurants.js"></script>

        <footer class="site footer">
            <p>Plan your day your way.</p>
            <p class="footer small">&copy; 2026 KMK Reservations</p>
        </footer>
    </body>
</html>