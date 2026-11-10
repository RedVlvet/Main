<?php
    // Start the session and checks the user is logged in.
    session_start();
    $isLoggedIn = isset($_SESSION['user_id']);
     
    //get the file name of the website
    $basepath = dirname(__DIR__);
    $base = str_replace('\\','/',$basepath);

    //User of ternary operary ($_POST would be true $_GET would mean false)
    $accomodation = $_POST['accomodation'] ?? $_GET['accomodation'] ?? '';
    
?>

<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> Accomodations | KMK Reservations</title>
        <link rel="stylesheet" href="KMKReservations_Accomodations.css">    
        <link rel ="stylesheet" href="MessagePopUps.css">
    </head>

    <body >
        <!-- Navigation Bar -->
        <header>
            <div>
                <nav>
                    <div class="navbar">
                        <img src="../KMKReservations_Logo.png" style="width:150px">
                        <a href="../Home/KMKReservations_Home.php">Home</a>
                        <a href="../Restaurants/KMKReservations_Restaurants.php">Restaurant</a>
                        <a class ="active" href="../Accomodations/KMKReservations_Accomodations.php">Accomodations</a>
                        <a href="../Drivers/KMKReservations_Drivers.php">Drivers</a>
                        <a href="../MyBookings/KMKReservations_MyBookings.php">My Bookings</a>
                        <a href="../MyProfile/KMKReservations_MyProfile.php">My Profile</a>
                    </div>
                </nav>
            </div>
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

                <!-- JavaScript to work with restaurant and driver reservatons/bookings -->
                <form
                    id="bookingForm"
                    action="../MyBookings/KMKReservations_MyBookings.php"
                    method="POST"
                    data-service="rooms"
                    data-logged-in="<?php echo $isLoggedIn ? 'true' : 'false'; ?>"
                    data-profile-url="../MyProfile/KMKReservations_MyProfile.php"
                >

                    <!-- used to identifies the service -->
                    <input type="hidden" name="service" value="rooms">

                    <!-- List of Accomodations-->
                    <div class = "form-group">
                        <label for = "accomodation"> Accomodations: </label>
                        <select id = "accomodation" name = "accomodation" required>
                            <option value = ""> Select an Accomodation </option>
                            <option value = "Bahia Principe Explore Jamaica " <?= $accomodation === 'Bahia Principe Explore Jamaica' ? 'selected' : '' ?>> Bahia Principe Explore Jamaica - Runaway Bay </option>
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
                        <input type="date" id="checkin" name="checkin" required>
                    </div>

                    <!-- Check-out Date -->
                    <div class="form-group">
                        <label for="checkout">Check-out:</label>
                        <input type="date" id="checkout" name="checkout" required>
                    </div>

                    <!-- Number of Adults -->
                    <div class="form-group">
                        <label for="adults">Adults:</label>
                        <select id="adults" name="adults" required>
                            <option value="1">1 Adult</option>
                            <option value="2">2 Adults</option>
                            <option value="3">3 Adults</option>
                            <option value="4">4 Adults</option>
                            <option value="5">5 Adults</option>
                            <option value="6">6 Adults</option>
                        </select>
                    </div>

                    <!-- Number of Children -->
                    <div class="form-group">
                        <label for="children">Children:</label>
                        <select id="children" name="children">
                            <option value="0">No Children</option>
                            <option value="1">1 Child</option>
                            <option value="2">2 Children</option>
                            <option value="3">3 Children</option>
                            <option value="4">4 Children</option>
                        </select>
                    </div>

                    <!-- Number of Rooms -->
                    <div class="form-group">
                        <label for="rooms">Number of Rooms:</label>

                        <select id="rooms" name="rooms" required>
                            <option value="1">1 Room</option>
                            <option value="2">2 Rooms</option>
                            <option value="3">3 Rooms</option>
                            <option value="4">4 Rooms</option>
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

        
        <!-- 

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

                    <button type="button" onclick="accountRequired()">
                        Test Account Required Pop-up
                    </button>
                </div>
            </div>
        </div> -->        

        <!-- Link the JavaScript file -->
        <script> src="MessagePopUps.js"></script>
        <script src="KMKReservations_MyBookings.js"></script>
        
        <!-- footer with copyright and slogan--> 
        <footer class="site-footer">
            <p>Plan your day your way.</p>
            <p class="footer-small">&copy; 2026 KMK Reservations</p>
        </footer>
    </body>
</html>
