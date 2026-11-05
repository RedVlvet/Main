<?php
// Start the session to check whether the user is logged in.
session_start();

// Check if the user has an account session.
// Change 'user_id' if your login system uses a different session name.
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rooms | KMK Reservations</title>
        <link rel="stylesheet" href="KMKReservations_Rooms.css">    
    </head>

    <body>
        <!-- Navigation Bar -->
        <header>
            <div>
                <nav>
                    <div class="navbar">
                        <img src="../KMKReservations_Logo.png" style="width:150px">
                        <a href="../KMKReservations_Home.php">Home</a>
                        <a href="../Restaurants/KMKReservations_Restaurants.php">Restaurant</a>
                        <a class ="active" href="KMKReservations_Rooms.php">Rooms</a>
                        <a href="../Drivers/KMKReservations_Chauffeur.php">Drivers</a>
                        <a href="../MyBookings/KMKReservations_MyBookings.php">My Bookings</a>
                        <a href="../MyProfile/KMKReservations_MyProfile.php" aria-label="MyProfile">&#128100;</a>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Main Rooms Section -->
        <main class="rooms-hero">

            <!-- Left Side: Welcome Message -->
            <section class="hero-content">
                <h1>Room Reservations</h1>
                <p>Find and reserve a comfortable room for your stay.</p>
            </section>


            <!-- Right Side: Booking Form -->
            <section class="booking-panel" id="booking-panel">
                <h2>Book Your Room</h2>

                <!--The data allows the same JavaScript to work with restaurant and driver bookings.-->
                <form
                    id="bookingForm"
                    action="process-booking.php"
                    method="POST"
                    data-service="rooms"
                    data-logged-in="<?php echo $isLoggedIn ? 'true' : 'false'; ?>"
                    data-profile-url="KMKReservations_MyProfile.php"
                >

                    <!-- Hidden field identifies the service -->
                    <input type="hidden" name="service" value="rooms">

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

        <!-- Login / Create Account Pop-up -->
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

        <?php include 'AccountCreation.php'; ?>

        <!-- Link the JavaScript file -->
        <script src="KMKReservations_MyBookings.js"></script>
    </body>
</html>
