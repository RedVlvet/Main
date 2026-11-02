<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> KMK Reservation Home Page </title>
        <link rel="stylesheet" href="KMKReservations_Home.css">
    </head>
        <body>    
            <nav>
                <div class="navbar">
                    <img src="KMKReservations_Logo.png" style="width:150px">
                    <a class = "active" href="KMKReservations_Home.php">Home</a>
                    <a href="KMKReservations_Restaurants.php">Restaurant</a>
                    <a href="KMKReservations_Rooms.php">Room</a>
                    <a href="KMKReservations_Chauffeur.php">Drivers</a>
                    <a href="KMKReservations_MyBookings.php">My Bookings</a>
                    <a href="KMKReservations_Account.php">Account</a>
                </div>
            </nav>

            <section style="text-align: center;">
                <div>
                    <h3>Your plans,perfectly reserved.</h3>
                    <p>Book restaurants, rooms and drivers all in one place.</p> 
                    <p>Fast, easy and reliable across Jamaica.</p>
                </div>
            </section>

            <!-- What the company does -->
            <section>
                <div class = "info">
                    <h1 >What We Do </h1>        
                    <ul>            
                        <li>
                            Restaurant Reservations
                            Book tables at restaurants based on your preferred date and time.
                        </li>
                        <br>   

                        <li>
                            Hotel Reservations
                            Find and reserve rooms for stays, whether for a short visit or longer trip.
                        </li>
                        <br>  
                        
                        <li>
                            Driver Reservations
                            Book a driver for transportation when you need a convenient way to get around.
                        </li>
                        <br>  

                        <li>
                            Appointment Booking
                            Schedule services and appointments without having to make multiple phone calls.
                        </li>
                        <br>

                        <li>
                            Booking Management
                            Keep track of your reservations and appointment details in one place.
                        </li>
                        <br>                                    
                    </ul>      
                </div>  
            </section>  

            <section class="services-section">

                <div class="services-heading">
                    <h1>What would you like to reserve?</h1>
                    <p>Select a service below to get started.</p>
                </div>

                <div class="services-container">

                    
                    <div class="service-card restaurant-card">

                        <div class="service-image">
                            <img src="RestaurantReservations.jpg" alt="Restaurant reservation">
                        </div>

                        <div class="service-content">
                            <h2>Restaurant Reservation</h2>
                            <p>
                                Find and reserve a table at your favourite restaurant.
                            </p>
                            <a href="KMKReservations_Restaurants.php" class="explore-btn">
                                Explore Service
                            </a>
                        </div>
                    </div>
                    
                    <div class="service-card room-card">

                        <div class="service-image">
                            <img src="RoomReservation.jpg" alt="Hotel room reservation">
                        </div>
                        <div class="service-content">
                            <h2>Room Reservation</h2>
                            <p>
                                Find and reserve a comfortable room for your stay.
                            </p>
                            <a href="KMKReservations_Rooms.php" class="explore-btn">
                                Explore Service
                            </a>
                        </div>
                    </div>

                    <!-- Driver Section -->
                    <div class="service-card driver-card">
                        <div class="service-image">
                            <img src="DriverReservation.jpeg" alt="Driver reservation">
                        </div>
                        <div class="service-content">
                            <h2>Driver Reservation</h2>
                            <p>
                                Book a reliable driver for your journey.
                            </p>
                            <a href="KMKReservations_Chauffeur.php" class="explore-btn">
                                Explore Service 
                            </a>
                        </div>
                    </div>
                </div>
            </section>

        <!-- What the company is about --> 
        <div>
            <h1> About Us </h1>

            </ol>
                <ul>
                    KMK Reservations is an online reservation and appointment booking platform designed to make planning easier and more convenient. 
                    Our goal is to give customers one simple place to manage reservations for restaurants, hotels, and drivers. 
                    We help users save time by making the booking process quick, organized, and accessible.
                </ul>
            </ol>
        </div>
        
        <!-- Draw a horizontal line to separate the content from the footer -->
        <hr> 
            
        <!-- footer with copyright and slogan-->    
        <footer class="site-footer">
            <p>Plan your day your way.</p>
            <p class="footer-small">&copy; 2026 KMK Reservations</p>
        </footer>
    </body>
</html>