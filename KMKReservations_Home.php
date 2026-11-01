<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> KMK Reservation Home Page </title>
    <link rel="stylesheet" href="KMKReservations_Home.css">
</head>
<body>

    <!-- Navigation of the website-->
        <nav>
            <div class="navbar">
                <img src="KMKReservations_Logo2.png" style="width:180px">
                <a class = "active" href="KMKReservations_Index.php">Home</a>
                <a href="KMKReservations_Restaurants.php">Restaurant</a>
                <a href="KMKReservations_Rooms.php">Room</a>
                <a href="KMKReservations_Chauffeur.php">Drivers</a>
                <a href="KMKReservations_MyBookings.php">My Bookings</a>
            </div>
        </nav>
    <section>
        <div class = "info">
            <ul>
                <h3>Your plans,perfectly reserved.</h3>
                <h3>Book restaurants, rooms and drivers all in one place.</h3> 
                <h3>Fast, easy and reliable across Jamaica.</h3>
            </ul>
        <!-- What the company does -->
        <section>
            <div class = "info">
                <h1 >What We Do </h1>        
                <ul>            
                    <li>
                        🍽️ Restaurant Reservations
                        Book tables at restaurants based on your preferred date and time.
                    </li>
                    <br>   

                    <li>
                        🏨 Hotel Reservations
                        Find and reserve rooms for stays, whether for a short visit or longer trip.
                    </li>
                    <br>  
                    
                    <li>
                        🚗 Driver Reservations
                        Book a driver for transportation when you need a convenient way to get around.
                    </li>
                    <br>  

                    <li>
                        📅 Appointment Booking
                        Schedule services and appointments without having to make multiple phone calls.
                    </li>
                    <br>

                    <li>
                        🔔 Booking Management
                        Keep track of your reservations and appointment details in one place.
                    </li>
                    <br>                                    
                </ul>        
            </div>  
        </section>

    <!-- Services -->
    <section class="services-section">

        <div class="services-heading">
            <h1>What would you like to reserve?</h1>
            <p>Select a service below to get started.</p>
        </div>

        <div class="services-container">

            <!-- Restaurant Card -->
            <div class="service-card restaurant-card">

                <div class="service-image">
                    <img src="RestaurantReservation.jpg" alt="Restaurant reservation">
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

            <!-- Room/Hotel Section -->
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
    <section>
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
    </section>

    <!-- Draws a horizontal line at the bottom of the page --> 
    <hr> 

    <!--Footer-->
    <footer>
        <div id="content" style="text-align: center;">
            <p>&copy; KMK Reservations 2026. All Rights Reserved. </p>
            <p>Plan your day your way.</p>
        </div>
    </footer>
</body>
</html>