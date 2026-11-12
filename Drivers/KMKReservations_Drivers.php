<?php
    // Start the session and checks the user is logged in.
    session_start();
    $isLoggedIn = isset($_SESSION['user_id']);

     //get the file name of the website
    $basepath = dirname(__DIR__);
    $base = str_replace('\\','/',$basepath);

    // ?? (null coalescing operator): use $_POST if it is set, if not use $_GET, if not use ''
    $driver = $_POST['driver'] ?? $_GET['driver'] ?? '';
    $time = $_POST['time'] ?? $_GET['time'] ?? '';
    $date = $_POST['date'] ?? '';
    $adults = $_POST['adults'] ?? '1';
    $children = $_POST['children'] ?? '0';

    // Trip details (like the Knutsford Express booking form)
    $from       = $_POST['from'] ?? '';
    $to         = $_POST['to'] ?? '';
    $tripType   = $_POST['trip_type'] ?? 'single';
    $returnDate = $_POST['return_date'] ?? '';
    $returnTime = $_POST['return_time'] ?? '';

    $error = '';
    $done  = false;

    // Places the user can travel from and to
    $locations = [
        "Kingston",
        "Norman Manley International Airport",
        "Spanish Town",
        "May Pen",
        "Mandeville",
        "Black River",
        "Negril",
        "Montego Bay",
        "Sangster International Airport",
        "Falmouth",
        "Ocho Rios",
        "Oracabessa",
        "Port Maria",
        "Port Antonio",
        "Morant Bay",
        "Stony Hill",
        "Moneague"
    ];

    // Database connection and shared functions
    require_once '../Database/DB.php';
    require_once '../Shared/KMKReservations_Functions.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!$isLoggedIn) {
            $error = 'Please log in or create an account before making a reservation.';
        } elseif ($driver == '' || $date == '' || $time == '' || $from == '' || $to == '') {
            $error = 'All fields are required.';
        } elseif (!in_array($from, $locations) || !in_array($to, $locations)) {
            $error = 'Please choose where you are travelling from and to.';
        } elseif ($from == $to) {
            $error = 'Travelling from and travelling to cannot be the same place.';
        } elseif ($date < date('Y-m-d')) {
            $error = 'The journey date cannot be in the past.';
        } elseif ($tripType == 'return' && ($returnDate == '' || $returnTime == '')) {
            $error = 'Please choose a return date and time.';
        } elseif ($tripType == 'return' && ($returnDate < $date || ($returnDate == $date && $returnTime <= $time))) {
            $error = 'The return trip must be after the journey.';
        } elseif ($adults + $children > 7) {
            $error = 'A car can only carry 7 passengers.';
        } elseif (($serviceId = getServiceId($conn, 'driver', $driver)) === null) {
            $error = 'Please choose a driver from the list.';
        } elseif (isAlreadyBooked($conn, $serviceId, $date, $time)) {
            // A driver can't be in two places at once
            $error = $driver . ' is already booked at that date and time. Please choose another time or driver.';
        } else {
            // Save to the database
            $reference = saveBooking($conn, $_SESSION['user_id'], $serviceId, [
                'booking_date' => $date,
                'booking_time' => $time,
                'end_date'     => $tripType == 'return' ? $returnDate : null,
                'end_time'     => $tripType == 'return' ? $returnTime : null,
                'adults'       => (int) $adults,
                'children'     => (int) $children,
                'from_place'   => $from,
                'to_place'     => $to,
                'trip_type'    => $tripType == 'return' ? 'return' : 'single'
            ]);

            if ($reference === false) {
                $error = 'Sorry, your booking could not be saved. Please try again.';
            } else {
                $done = true;
                // Clear the form after a successful booking
                $driver = $date = $time = $from = $to = $returnDate = $returnTime = '';
                $tripType = 'single';
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
        <title>Drivers | KMK Reservations</title>
        <link rel="stylesheet" href="KMKReservations_Drivers.css">
        <link rel ="stylesheet" href="../Shared/MessagePopUps.css">
    </head>
    <!-- data-done tells the JavaScript to show the "Booking Confirmed" pop-up -->
    <body data-done="<?php echo $done ? '1' : '0'; ?>">
         <!-- Navigation Bar -->
        <header class="site-nav">
            <a href="../Home/KMKReservations_Home.php" class="nav-brand">
                <img src="../KMKReservations_Logo.png" alt="KMK Reservations logo">
            </a>
            <nav class="nav-links">
                <a href="../Home/KMKReservations_Home.php">Home</a>
                <a href="../Restaurants/KMKReservations_Restaurants.php">Restaurant</a>
                <a href="../Accomodations/KMKReservations_Accomodations.php">Accommodations</a>
                <a class="active" href="../Drivers/KMKReservations_Drivers.php">Drivers</a>
                <a href="../MyBookings/KMKReservations_MyBookings.php">My Bookings</a>
                <a href="../MyProfile/KMKReservations_MyProfile.php">My Profile</a>
                <!-- Admin link: only admins see it -->
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a href="../Admin/KMKReservations_Admin.php">Admin</a>
                <?php endif; ?>
            </nav>
        </header>

        <!-- Main Drivers Section -->
        <main class="drivers-hero">

            <!-- Left Side: Welcome Message -->
            <section class="hero-content">
                <h1>Drivers Reservations</h1>
                <p> Book a reliable driver for your journey. </p>
            </section>
        
            <!-- Right Side: Reserving Form -->
            <section class="reserve-panel" id="reserve-panel">
                <!-- Form heading with a car icon (like "Book a trip" on Knutsford Express) -->
                <h2 class="trip-heading">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 17h14M3 13l2-6h14l2 6v4H3z"/>
                        <circle cx="7.5" cy="17" r="1.5"/>
                        <circle cx="16.5" cy="17" r="1.5"/>
                    </svg>
                    Reserve your driver
                </h2>

                <!-- Error message from PHP -->
                <?php if ($error != ''): ?>
                    <p class="alert-error"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <!--The data allows the same JavaScript to work with restaurant and driver bookings.-->
                <form
                    id="bookingForm"
                    action=""
                    method="POST"
                    data-service="drivers"
                    data-logged-in="<?php echo $isLoggedIn ? 'true' : 'false'; ?>"
                    data-profile-url="../MyProfile/KMKReservations_MyProfile.php"
                >

                    <!-- Hidden field identifies the service -->
                    <input type="hidden" name="service" value="drivers">

                    <!-- Name of the Drivers -->
                    <div class = "form-group">
                        <label for = "driver"> Select a Driver: </label>
                        <select id = "driver" name = "driver" required>
                            <option value = ""> Select a driver for your journey  </option>
                            <option value = "Marcus" <?= $driver === 'Marcus' ? 'selected' : '' ?>>Marcus</option>
                            <option value = "John" <?= $driver === 'John' ? 'selected' : '' ?>>John</option>
                            <option value = "Phillip" <?= $driver === 'Phillip' ? 'selected' : '' ?>>Phillip</option>
                            <option value = "Martin" <?= $driver === 'Martin' ? 'selected' : '' ?>>Martin</option>
                            <option value = "Francis" <?= $driver === 'Francis' ? 'selected' : '' ?>>Francis</option>
                        </select>
                    </div>

                    <!-- Travelling from and to (joined boxes with a swap button) -->
                    <div class="route-box">
                        <div class="route-field">
                            <label for="from">Travelling from</label>
                            <select id="from" name="from" required>
                                <option value="">Choose a pick-up place</option>
                                <?php foreach ($locations as $place): ?>
                                    <option value="<?= htmlspecialchars($place) ?>" <?= $from === $place ? 'selected' : '' ?>><?= htmlspecialchars($place) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="route-field">
                            <label for="to">Travelling to</label>
                            <select id="to" name="to" required>
                                <option value="">Choose a drop-off place</option>
                                <?php foreach ($locations as $place): ?>
                                    <option value="<?= htmlspecialchars($place) ?>" <?= $to === $place ? 'selected' : '' ?>><?= htmlspecialchars($place) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Swaps the from and to places -->
                        <button type="button" class="swap-btn" id="swapBtn" aria-label="Swap travelling from and to">&#8645;</button>
                    </div>

                    <!-- Date and Time of the Journey (side by side) -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="date"> Journey Date: </label>
                            <input type="date" id="date" name="date" value="<?= htmlspecialchars($date) ?>" required>
                        </div>

                        <div class = "form-group">
                            <label for = "time"> Time: </label>
                            <input type = "time" id="time" name = "time" value = "<?= htmlspecialchars($time) ?>" required>
                        </div>
                    </div>

                    <!-- Single or Return trip -->
                    <div class="trip-type">
                        <label><input type="radio" name="trip_type" value="single" <?= $tripType !== 'return' ? 'checked' : '' ?>> Single</label>
                        <label><input type="radio" name="trip_type" value="return" <?= $tripType === 'return' ? 'checked' : '' ?>> Return</label>
                    </div>

                    <!-- Return Date and Time (only shown for a Return trip) -->
                    <div class="form-row" id="returnRow" <?= $tripType === 'return' ? '' : 'hidden' ?>>
                        <div class="form-group">
                            <label for="return_date"> Return Date: </label>
                            <input type="date" id="return_date" name="return_date" value="<?= htmlspecialchars($returnDate) ?>">
                        </div>

                        <div class="form-group">
                            <label for="return_time"> Return Time: </label>
                            <input type="time" id="return_time" name="return_time" value="<?= htmlspecialchars($returnTime) ?>">
                        </div>
                    </div>

                    <!-- Passengers: click to choose (like Knutsford Express) -->
                    <button type="button" class="passenger-btn" id="passengerBtn" aria-expanded="false">
                        <span class="passenger-label">Passengers</span>
                        <span id="passengerSummary">1 Adult, No Children</span>
                    </button>

                    <div class="passenger-box" id="passengerBox">
                   
                    <!-- Number of Adults -->
                    <div class="form-group">
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

                    <!-- Number of Children -->
                    <div class="form-group">
                        <label for="children">Children:</label>
                        <select id="children" name="children">
                            <option value="0" <?= $children == '0' ? 'selected' : '' ?>>No Children</option>
                            <option value="1" <?= $children == '1' ? 'selected' : '' ?>>1 Child</option>
                            <option value="2" <?= $children == '2' ? 'selected' : '' ?>>2 Children</option>
                            <option value="3" <?= $children == '3' ? 'selected' : '' ?>>3 Children</option>
                            <option value="4" <?= $children == '4' ? 'selected' : '' ?>>4 Children</option>
                        </select>
                    </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="reserve-btn">
                        Check Availability
                    </button>
                </form>
            </section>
        </main>            
       
        

        <!-- Pop-up message box (used by MessagePopUps.js) -->
        <div id="bookingModal" class="modal-overlay">
            <div class="modal-box">
                <button
                    type="button"
                    id="modalClose"
                    class="close-btn"
                    aria-label="Close pop-up">
                    &times;
                </button>

                <div id="modalIcon" class="modal-icon">
                    !
                </div>

                <h2 id="modalTitle" class="modal-title">
                    Message Title
                </h2>

                <p id="modalMessage" class="modal-message">
                    Message will appear here.
                </p>

                <div class="modal-actions">

                    <button
                        type="button"
                        id="modalPrimary"
                        class="modal-button">
                        Continue
                    </button>

                    <button
                        type="button"
                        id="modalSecondary"
                        class="modal-button secondary">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- Link the JavaScript file -->
        <script src="../Shared/MessagePopUps.js"></script>
        <script src="KMKReservations_Drivers.js"></script>
       
        
        <!-- footer with copyright and slogan-->    
        <footer class="site-footer">
            <p>Plan your day your way.</p>
            <p class="footer-small">&copy; 2026 KMK Reservations</p>
        </footer>
    </body>
</html>
