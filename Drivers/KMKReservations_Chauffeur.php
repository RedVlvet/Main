<?php
    session_start();

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Drivers | KMK Reservations</title>
        <link rel="stylesheet" href="KMKReservations_Chauffeur.css">
    </head>
    <body> 
        
        

        <header class="site-header">        
            <nav>
                <div class="navbar">
                <img src="../KMKReservations_Logo.png" style="width:150px">
                <a href="../KMKReservations_Home.php">Home</a>
                <a href="../Restaurants/KMKReservations_Restaurants.php">Restaurant</a>
                <a href="../Rooms/KMKReservations_Rooms.php">Rooms</a>
                <a class ="active" href="../Drivers/KMKReservations_Chauffeur.php">Drivers</a>
                <a href="../MyBookings/KMKReservations_MyBookings.php">My Bookings</a>
                <a href="../MyProfile/KMKReservations_MyProfile.php" aria-label="MyProfile">&#128100;</a>
                </div>
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
