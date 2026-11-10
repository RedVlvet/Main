<?php
    // Start the session and checks the user is logged in.
    session_start();
    $isLoggedIn = isset($_SESSION['user_id']);

     //get the file name of the website
    $basepath = dirname(__DIR__);
    $base = str_replace('\\','/',$basepath);

    //User of ternary operary ($_POST would be true $_GET would mean false)
    $driver = $_POST['driver'] ?? $_GET['driver'] ?? '';
    $time = $_POST['time'] ?? $_GET['time'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Drivers | KMK Reservations</title>
        <link rel="stylesheet" href="KMKReservations_Drivers.css">
        <link rel ="stylesheet" href="MessagePopUps.css">
    </head>
    <body> 
         <!-- Navigation Bar -->
        <header>
            <div>
                <nav>
                    <div class="navbar">
                        <img src="../KMKReservations_Logo.png" style="width:150px">
                        <a href="../Home/KMKReservations_Home.php">Home</a>
                        <a href="../Restaurants/KMKReservations_Restaurants.php">Restaurant</a>
                        <a href="../Accomodations/KMKReservations_Accomodations.php">Accomodations</a>
                        <a class ="active" href="../Drivers/KMKReservations_Drivers.php">Drivers</a>
                        <a href="../MyBookings/KMKReservations_MyBookings.php">My Bookings</a>
                        <a href="../MyProfile/KMKReservations_MyProfile.php">My Profile</a>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Main Restaurants Section -->
        <main class="drivers-hero">

            <!-- Left Side: Welcome Message -->
            <section class="hero-content">
                <h1>Drivers Reservations</h1>
                <p> Book a reliable driver for your journey. </p>
            </section>
        
            <!-- Right Side: Reserving Form -->
            <section class="reserve-panel" id="reserve-panel">
                <h2> Reserve your driver </h2>

                <!--The data allows the same JavaScript to work with restaurant and driver bookings.-->
                <form
                    id="bookingForm"
                    action="../MyBookings/KMKReservations_MyBookings.php"
                    method="POST"
                    data-service="restaurants"
                    data-logged-in="<?php echo $isLoggedIn ? 'true' : 'false'; ?>"
                    data-profile-url="../MyProfile/KMKReservations_MyProfile.php"
                >

                    <!-- Hidden field identifies the service -->
                    <input type="hidden" name="service" value="drivers">

                    <!-- Name of the Drivers -->
                    <div class = "form-group">
                        <label for = "drivers"> Select a Route: </label>
                        <select id = "driver" name = "driver" required>
                            <option value = ""> Select a driver for your journey  </option>
                            <option value = "Marcus - Ocho Rios to Montego Bay" <?= $driver === 'Marcus - Oracabessa to Montego Bay' ? 'selected' : '' ?>>Marcus - Oracabessa to Montego Bay</option>
                            <option value = "John - Kingston to St.Thomas" <?= $driver === 'John - Kingston to St.Thomas' ? 'selected' : '' ?>>John - Kingston to St.Thomas</option>
                            <option value = "Phillip - Clarendon - St.Elizabeth" <?= $driver === 'Phillip - Clarendon - St.Elizabeth' ? 'selected' : '' ?>>Food to go - Ocho Rios</option>
                            <option value = "Martin - Stony Hill to Moneague " <?= $driver === 'Martin - Stony Hill to Moneague' ? 'selected' : '' ?>>Martin - Stony Hill to Moneague</option>
                            <option value = "Francis - Spanish Town to Negril" <?= $driver === 'Francis - Spanish Town to Negril' ? 'selected' : '' ?>> Francis - Spanish Town to Negril </option>
                        </select>
                    </div>

                    

                    <!-- Date of the Reservation -->
                    <div class="form-group">
                        <label for="driver"> Journey Date: </label>
                        <input type="date" id="reserve" name="reserve" required>
                    </div>

                    <!-- Time of the reservation -->
                    <div class = "form-group">
                        <label for = "driver"> Time for your Journey </label>
                        <input type = "time" id="time" name = "time" value = "<?= htmlspecialchars($time) ?>" required>
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
                    
                    <!-- Submit Button -->
                    <button type="submit" class="reserve-btn">
                        Check Availability
                    </button>
                </form>
            </section>
        </main>            
       
        
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
