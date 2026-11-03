<?php
    session_start(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurants | KMK Reservations</title>
    <link rel="stylesheet" href="KMKReservations_Restaurants.css">
    
</head>
    <body>
       
        <nav>
            <div class="navbar">
                <img src="KMKReservations_Logo.png" style="width:150px">
                <a href="KMKReservations_Home.php">Home</a>
                <a class = "active" href="KMKReservations_Restaurants.php">Restaurant</a>
                <a href="KMKReservations_Rooms.php">Room</a>
                <a href="KMKReservations_Chauffeur.php">Drivers</a>
                <a href="KMKReservations_MyBookings.php">My Bookings</a>
                <a href="KMKReservations_Account.php">Account</a>

            </div>
        </nav>
    
        
      <section id="restaurant" class="service-section">
            <div class="image-container">
                <img src="RestaurantReservations.jpg" alt="Restaurant Image">
                <div class="image-text">
                    <h1>Restaurant Reservations</h1>
                    <p>Find and reserve a table at your favourite restaurant.</p>
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