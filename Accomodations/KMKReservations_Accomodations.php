<?php
    // Start the session and checks the user is logged in.
    session_start();
    $isLoggedIn = isset($_SESSION['user_id']);
     
    //get the file name of the website
    $basepath = dirname(__DIR__);
    $base = str_replace('\\','/',$basepath);

    // ?? (null coalescing operator): use $_POST if it is set, if not use $_GET, if not use ''
    $accomodation = $_POST['accomodation'] ?? $_GET['accomodation'] ?? '';
    $checkin  = $_POST['checkin'] ?? '';
    $checkout = $_POST['checkout'] ?? '';
    $adults   = $_POST['adults'] ?? '1';
    $children = $_POST['children'] ?? '0';
    $rooms    = $_POST['rooms'] ?? '1';
    $error = '';
    $done  = false;

    // Database connection and shared functions
    require_once '../Database/DB.php';
    require_once '../Shared/KMKReservations_Functions.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!$isLoggedIn) {
            $error = 'Please log in or create an account before making a reservation.';
        } elseif ($accomodation == '' || $checkin == '' || $checkout == '') {
            $error = 'All fields are required.';
        } elseif ($checkin < date('Y-m-d')) {
            $error = 'Check-in cannot be in the past.';
        } elseif ($checkout <= $checkin) {
            $error = 'Check-out must be after check-in.';
        } elseif ($rooms > $adults) {
            $error = 'Each room needs at least one adult.';
        } elseif (($serviceId = getServiceId($conn, 'room', $accomodation)) === null) {
            $error = 'Please choose an accommodation from the list.';
        } elseif (isAlreadyBooked($conn, $serviceId, $checkin, null, $_SESSION['user_id'])) {
            $error = 'You already have a booking here for that check-in date.';
        } else {
            // Save to the database
            $reference = saveBooking($conn, $_SESSION['user_id'], $serviceId, [
                'booking_date' => $checkin,
                'end_date'     => $checkout,
                'adults'       => (int) $adults,
                'children'     => (int) $children,
                'rooms'        => (int) $rooms
            ]);

            if ($reference === false) {
                $error = 'Sorry, your booking could not be saved. Please try again.';
            } else {
                $done = true;
                // Clear the form after a successful booking
                $accomodation = $checkin = $checkout = '';
                $adults = '1';
                $children = '0';
                $rooms = '1';
            }
        }
    }
?>

<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> Accommodations | KMK Reservations</title>
        <link rel="stylesheet" href="KMKReservations_Accomodations.css">    
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
                <a class="active" href="../Accomodations/KMKReservations_Accomodations.php">Accommodations</a>
                <a href="../Drivers/KMKReservations_Drivers.php">Drivers</a>
                <a href="../MyBookings/KMKReservations_MyBookings.php">My Bookings</a>
                <a href="../MyProfile/KMKReservations_MyProfile.php">My Profile</a>
                <!-- Admin link: only admins see it -->
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a href="../Admin/KMKReservations_Admin.php">Admin</a>
                <?php endif; ?>
            </nav>
        </header>

        <!-- Main Rooms Section -->
        <main class="accomodation-hero">

            <!-- Left Side: Welcome Message -->
            <section class="hero-content">
                <h1>Room Reservations</h1>
                <p>Find and reserve a comfortable room for your stay.</p>
            </section>


            <!-- Right Side of the Booking Form -->
            <section class="booking-panel" id="booking-panel">
                <h2>Book Your Room</h2>

                <!-- Error message from PHP -->
                <?php if ($error != ''): ?>
                    <p class="alert-error"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <!-- JavaScript to work with restaurant and driver reservatons/bookings -->
                <form
                    id="bookingForm"
                    action=""
                    method="POST"
                    data-service="rooms"
                    data-logged-in="<?php echo $isLoggedIn ? 'true' : 'false'; ?>"
                    data-profile-url="../MyProfile/KMKReservations_MyProfile.php"
                >

                    <!-- used to identifies the service -->
                    <input type="hidden" name="service" value="rooms">

                    <!-- List of Accomodations-->
                    <div class = "form-group">
                        <label for = "accomodation"> Accommodations: </label>
                        <select id = "accomodation" name = "accomodation" required>
                            <option value = ""> Select an Accommodation </option>
                            <option value = "Bahia Principe Explore Jamaica" <?= $accomodation === 'Bahia Principe Explore Jamaica' ? 'selected' : '' ?>> Bahia Principe Explore Jamaica - Runaway Bay </option>
                            <option value = "The Blue House Boutique Bed & Breakfast Villa" <?= $accomodation === 'The Blue House Boutique Bed & Breakfast Villa' ? 'selected' : '' ?>>The Blue House Boutique Bed & Breakfast Villa - 30 Marcliff Drive, Ocho Rios</option>
                            <option value = "Royalton Blue Waters" <?= $accomodation === 'Royalton Blue Waters' ? 'selected' : '' ?>>Royalton Blue Waters - Trelawny </option>
                            <option value = "Golden Eye" <?= $accomodation === 'Golden Eye' ? 'selected' : '' ?>> Golden Eye - Race Course, Oracabessa </option>
                            <option value = "Couples Sans Souci" <?= $accomodation === 'Couples Sans Souci' ? 'selected' : '' ?>> Couples Sans Souci - White River, St.Ann </option>
                            <option value = "Sandals Jamaica Montego Bay" <?= $accomodation === 'Sandals Jamaica Montego Bay' ? 'selected' : '' ?>> Sandals Jamaica Montego Bay - Kent Ave, Montego Bay </option>
                        </select>
                    </div>

                    <!-- Check-in Date -->
                    <div class="form-group">
                        <label for="checkin">Check-in:</label>
                        <input type="date" id="checkin" name="checkin" value="<?= htmlspecialchars($checkin) ?>" required>
                    </div>

                    <!-- Check-out Date -->
                    <div class="form-group">
                        <label for="checkout">Check-out:</label>
                        <input type="date" id="checkout" name="checkout" value="<?= htmlspecialchars($checkout) ?>" required>
                    </div>

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

                    <!-- Number of Rooms -->
                    <div class="form-group">
                        <label for="rooms">Number of Rooms:</label>

                        <select id="rooms" name="rooms" required>
                            <option value="1" <?= $rooms == '1' ? 'selected' : '' ?>>1 Room</option>
                            <option value="2" <?= $rooms == '2' ? 'selected' : '' ?>>2 Rooms</option>
                            <option value="3" <?= $rooms == '3' ? 'selected' : '' ?>>3 Rooms</option>
                            <option value="4" <?= $rooms == '4' ? 'selected' : '' ?>>4 Rooms</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="booking-btn">
                        Check Availability
                    </button>
                </form>
            </section>
        </main>

        <!--
        //Login / Create Account Pop-up 
        <div class="modal-overlay" id="accountModal">

            <div class="modal-box">
                <button type="button" class="close-btn" id="closeModal">&times;</button>
                <div class="modal-icon">!</div>
                <h2>Account Required</h2>
                <p>
                    You need to have an account before making a reservation.
                    Please visit the My Profile section to create an account
                    or log in to continue.
                </p>
                <button type="button" class="profile-btn" id="profileBtn">Go to My Profile</button>
                <button type="button" class="cancel-btn" id="cancelBtn">Maybe Later</button>
            </div>
        </div>
        -->

        
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
        
        <!-- footer with copyright and slogan--> 
        <footer class="site-footer">
            <p>Plan your day your way.</p>
            <p class="footer-small">&copy; 2026 KMK Reservations</p>
        </footer>
    </body>
</html>
