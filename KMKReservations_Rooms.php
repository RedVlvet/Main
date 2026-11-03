<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> KMK Reservation Rooms Page </title>
    <link rel="stylesheet" href="KMKReservations_Home.css">
    <link rel="stylesheet" href="KMKReservations_Rooms.css">
    
</head>
    <body>
        
        <header class="site-nav">
            <a href="KMKReservations_Home.php" class="nav-brand">
                <img src="KMKReservations_Logo.png" alt="KMK Reservations logo">
            </a>
            <nav class="nav-links">
                <a href="KMKReservations_Home.php">Home</a>
                <a href="KMKReservations_Restaurants.php">Restaurant</a>
                <a class="active" href="KMKReservations_Rooms.php">Rooms</a>
                <a href="KMKReservations_Chauffeur.php">Drivers</a>
                <a href="KMKReservations_MyBookings.php">My Bookings</a>
                <a href="KMKReservations_Account.php" class="nav-account" aria-label="My account">&#128100;</a>
            </nav>
        </header>
 
        <section id="room" class="service-section">
            <div class="image-container">
                <img src="RoomReservation.jpg" alt="Room Image">

                <div class="image-text">
                    <h1>Room Reservations</h1>
                    <p>Find and reserve a comfortable room for your stay.</p>
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