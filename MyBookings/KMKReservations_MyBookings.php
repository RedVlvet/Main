<?php
    // Start the session and checks the user is logged in.
    session_start();
    $isLoggedIn = isset($_SESSION['user_id']);

    // require_once 'DB.php';   
    $basepath = dirname(__DIR__);
    $base = str_replace('\\','/',$basepath);

    //Uses the name of the user currently logged in
    $userName = $_SESSION['user_name'] ?? 'User';

    // An array that stores all reservation made
    $reservations =
    ["restaurants" => [],
    "rooms" => [],
    "drivers" => []
    ];

    //retrieves records belonging to a user
    $_SESSION['user_id']
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
        <header>
            <div>
                <nav>
                    <div class="navbar">
                        <img src="../KMKReservations_Logo.png" style="width:150px">
                        <a href="../Home/KMKReservations_Home.php">Home</a>
                        <a href="../Restaurants/KMKReservations_Restaurants.php">Restaurant</a>
                        <a href="../Accomodations/KMKReservations_Accomodations.php">Accomodations</a>
                        <a href="../Drivers/KMKReservations_Drivers.php">Drivers</a>
                        <a class ="active" href="../MyBookings/KMKReservations_MyBookings.php">My Bookings</a>
                        <a href="../MyProfile/KMKReservations_MyProfile.php">My Profile</a>
                    </div>
                </nav>
            </div>
        </header>  
        
        <main class = "container">

            <!--Welcome Message -->
            <section class = "welcome">
                <h1> Hi, <?php echo htmlspecialchars($userName); ?>! </h1>
                <p>
                    These are your current and previous reservations.
                    Where you can view in one place.
                </p>
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
                <div class = "rerservation-tabs">
                    <button class = "tab active" data-status = "current">
                        Current Reservations
                    </button>

                    <button class = "tab" data-source = "previous">
                        Previous Reservations
                    </button>
                </div> 

                <!-- Reservation Cards -->
                <div class = "reservation-list">
                    <?php foreach ($reservations as $reservation): ?>
                        <div class = "reservation-card"
                        data-service = "<?php echo $reservation['service']; ?>"
                        data-status = "<?php echo $reservation['status']; ?>">

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
                        
                        <!-- Display the reservation status (Current or Previous) -->
                        <span class = "status"> 
                            <!-- Capitalises the first letter of the reservation status --> 
                            <?php echo ucfirst($reservation['status']); ?>
                        </span>
                    </div>

                    <!-- End the foreach loop --> 
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
        <script> src="MessagePopUps.js"></script>
        <script> src="KMKReservation_MyBookings.js"></script>         

       <!-- footer with copyright and slogan-->    
        <footer class="site-footer">
            <p>Plan your day your way.</p>
            <p class="footer-small">&copy; 2026 KMK Reservations</p>
        </footer>
    </body>
</html>