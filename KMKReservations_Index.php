<?php
// KMK Reservations - Home Page

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KMK Reservations | Plan Your Day Your Way</title>
    <link rel="stylesheet" href="KMKReservations_Index.css">
</head>
<body>
    

<header class="site-header">  

    <nav class="main-nav" aria-label="Main navigation">
        <img src="KMKReservations_Logo.png" style="height:130px;width:130px">
        <a class="active" href="KMKReservations_Index.php">Home</a>
        <a href="KMKReservations_Restaurants.php">Restaurants</a>
        <a href="KMKReservations_Rooms.php">Rooms</a>
        <a href="KMKReservations_Chauffeurs.php">Drivers</a>
        <a href="KMKReservations_MyBookings.php">My Bookings</a>
    </nav>
</header>

<main>
    <section class="hero">       
    <section class="services-section" id="services">
        <div class="section-heading">
            <p class="eyebrow">CHOOSE YOUR NEXT STEP</p>
            <h2>What would you like to reserve?</h2>
            <p>Select a service below to get started.</p>
        </div>
    </section>

    <section>
        <article class="service-card restaurant-card">
            <img src = "RestaurantReservation.jpg" style = "height: 150px; width: 250px" > <br>
            <h2>Restaurant Reservations</h2>
            <p>Save a seat at your favourite spot.</p>
            <a class="btn btn-light" href="reservations.php">Book now →</a>
        </article>
        <article class="service-card room-card">
            <img src = "RoomReservation.jpg" style = "height: 150px; width: 250px"> <br>
            <h2>Room Reservations</h2>
            <p>Find a stay for every kind of trip.</p>
            <a class="btn btn-light" href="rooms.php">Book now →</a>
        </article>
        <article class="service-card driver-card">
            <img src = "DriverReservation.jpeg" style = "height: 150px; width: 250px"> <br>
            <h2>Driver Reservations</h2>
            <p>Get there safely and on time.</p>
            <a class="btn btn-light" href="drivers.php">Book now →</a>
        </article>    
    </section>    
</main>

<footer class="site-footer">
    <p>&copy;KMK Reservations.</p>
    <p>Plan your day your way.</p>
</footer>

</body>
</html>
