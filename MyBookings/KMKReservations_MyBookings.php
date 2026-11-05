<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings | KMK Reservations</title>
    <link rel="stylesheet" href="KMKReservations_MyBookings.css">
    
</head>
    <body>
       
        <nav>
            <div class="navbar">
                <a href="../KMKReservations_Home.php">Home</a>
                <a href="../Restaurants/KMKReservations_Restaurants.php">Restaurant</a>
                <a href="../Rooms/KMKReservations_Rooms.php">Rooms</a>
                <a href="../Drivers/KMKReservations_Chauffeur.php">Drivers</a>
                <a class ="active" href="MyBookings/KMKReservations_MyBookings.php">My Bookings</a>
                <a href="../MyProfile/KMKReservations_MyProfile.php" aria-label="MyProfile">&#128100;</a>
            </div>
        </nav>
        
        
        <section id="bookings" class="service-section">
            <div class="image-container banner">
                <img src="Bookings.jpg" alt="My Bookings">

                <div class="image-text">
                    <h1>My Bookings</h1>
                    <p>View and manage all your reservations in one place.</p>
                </div>
            </div>

            <!--bookings list/table-->
            <div class="bookings-list">
                
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