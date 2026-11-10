<?php
    // Start the session and checks the user is logged in.
    session_start();
    $isLoggedIn = isset($_SESSION['user_id']);
    
    // require_once 'DB.php';   
    $basepath = dirname(__DIR__);
    $base = str_replace('\\','/',$basepath);

    $name = $_SESSION['user_name'] ?? '';
    $restaurant = '';
    $date = $time = $guests = '';
    $error = '';
    $done  = false;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //$name       = trim($_POST['name']);
        //not sure if the above will be needed seeing that the user would have to login 
        //they would see their name in my booking 
        $restaurant = trim($_POST['restaurant']);
        $date       = $_POST['date'];
        $time       = $_POST['time'];
        $guests     = $_POST['guests'];
    
        //place $name == '' || before restaurants 
        //not sure if the above will be needed seeing that the user would have to login
        //they would see their name in my booking
        if ($restaurant == '' || $date == '' || $time == '' || $guests == '') {
            $error = 'All fields are required.';
        } elseif ($date < date('Y-m-d')) {
            $error = 'The date cannot be in the past.';
        } else {
            // Save to the database with DB.php
            $done = true;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Restaurants | KMK Reservations</title>
        <link rel="stylesheet" href="KMKReservations_Restaurants.css">   
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
                        <a class ="active" href="../Restaurants/KMKReservations_Restaurants.php">Restaurant</a>
                        <a href="../Accomodations/KMKReservations_Accomodations.php">Accomodations</a>
                        <a href="../Drivers/KMKReservations_Drivers.php">Drivers</a>
                        <a href="../MyBookings/KMKReservations_MyBookings.php">My Bookings</a>
                        <a href="../MyProfile/KMKReservations_MyProfile.php">My Profile</a>
                    </div>
                </nav>
            </div>
        </header>
        
        <!-- Main Restaurants Section -->
        <main class="restaurants-hero">

            <!-- Left Side: Welcome Message -->
            <section class="hero-content">
                <h1>Restaurant Reservations</h1>
                <p>Find and reserve a table at your favourite restaurant.</p>
            </section>


            <!-- Right Side: Reserving Form -->
            <section class="reserve-panel" id="reserve-panel">
                <h2> Reserve your table </h2>

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
                    <input type="hidden" name="service" value="restaurants">

                    <!-- Name of the Restaurant -->
                    <div class = "form-group">
                        <label for = "restaurant"> Select restaurant: </label>
                        <select id = "restaurant" name = "restaurant" required>
                            <option value = ""> Select a restaurant </option>
                            <option value = "The Jerk Centre" <?= $restaurant === 'The Jerk Centre' ? 'selected' : '' ?>>The Jerk Centre - Ocho Rios</option>
                            <option value = "Pier 1 Restaurant" <?= $restaurant === 'Pier 1 Restaurant' ? 'selected' : '' ?>>Pier 1 Restaurant -Howard Cooke Boulevard</option>
                            <option value = "Food to go" <?= $restaurant === 'Food to go' ? 'selected' : '' ?>>Food to go - Ocho Rios</option>
                            <option value = "Sally's at Sunset" <?= $restaurant === "Sally's at Sunset" ? 'selected' : '' ?>>Sally's at Sunset - Negril</option>
                            <option value = "Cabarita Inn" <?= $restaurant === 'Cabarita Inn' ? 'selected' : '' ?>> Cabarita Inn - Port Maria </option>
                            <option value = "Roots 21 | Bar & Kitchen" <?= $restaurant === 'Roots 21 | Bar & Kitchen' ? 'selected' : '' ?>> Roots 21 | Bar & Kitchen - Port Antonio </option>
                        </select>
                    </div>

                    <!-- Date of the Reservation -->
                    <div class="form-group">
                        <label for="reservation"> Reservation Date: </label>
                        <input type="date" id="reserve" name="reserve" required>
                    </div>

                    <!-- Time of the reservation -->
                    <div class = "form-group">
                        <label for = "time"> Time for your reservation </label>
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
        <script src="MessagePopUps.js"></script>
        <script src="KMKReservations_MyBookings.js"></script>        

        <!-- footer with copyright and slogan-->    
        <footer class="site-footer">
            <p>Plan your day your way.</p>
            <p class="footer-small">&copy; 2026 KMK Reservations</p>
        </footer>
    </body>
</html>