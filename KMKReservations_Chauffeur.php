<?php
    session_start();

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>KMK Reservations Drivers </title>
        <link rel="stylesheet" href="KMKReservations_Home.css">
        <link rel="stylesheet" href="KMKReservations_Chauffeur.css">
    </head>
    <body> 
        
        

        <header class="site-nav">
            <a href="KMKReservations_Home.php" class="nav-brand">
                <img src="KMKReservations_Logo.png" alt="KMK Reservations logo">
            </a>
            <nav class="nav-links">
                <a href="KMKReservations_Home.php">Home</a>
                <a href="KMKReservations_Restaurants.php">Restaurant</a>
                <a href="KMKReservations_Rooms.php">Room</a>
                <a class="active" href="KMKReservations_Chauffeur.php">Drivers</a>
                <a href="KMKReservations_MyBookings.php">My Bookings</a>
                <a href="KMKReservations_Account.php" class="nav-account" aria-label="My account">&#128100;</a>
            </nav>
        </header>

        
            <section id="driver" class="service-section">
                <div class="image-container">
                    <img src="DriverReservation.jpeg" alt="Driver Image">

                    <div class="image-text">
                        <h1>Driver Reservations</h1>
                        <p>Book a reliable driver for your journey.</p>
                    </div>
                </div>
            </section>
            
        <hr>
        
        <!-- footer with copyright and slogan-->    
        <footer class="site-footer">
            <p>Plan your day your way.</p>
            <p class="footer-small">&copy; 2026 KMK Reservations</p>
        </footer>
    </body>
</html>
