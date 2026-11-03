<?php
    session_start(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> KMK Reservation Restaurants </title>
    <link rel="stylesheet" href="KMKReservations_Home.css">
    <link rel="stylesheet" href="KMKReservations_Restaurants.css">
    
</head>
    <body >
       
        <header class="site-nav">
            <a href="KMKReservations_Home.php" class="nav-brand">
                <img src="KMKReservations_Logo.png" alt="KMK Reservations logo">
            </a>
            <nav class="nav-links">
                <a href="KMKReservations_Home.php">Home</a>
                <a href="KMKReservations_Restaurants.php" class="active">Restaurants</a>
                <a href="KMKReservations_Rooms.php">Rooms</a>
                <a href="KMKReservations_Chauffeur.php">Drivers</a>
                <a href="KMKReservations_MyBookings.php">My Bookings</a>
                <a href="KMKReservations_Account.php" class="nav-account" aria-label="My account">&#128100;</a>
            </nav>
        </header>
         <!-- This is the page banner -->
        <main class="restaurant-main">
            <section id="restaurant" class="Restaurant_Banner" aria-labelledby="restaurant-title">
                
                <div class="Restaurant_text">
                    <h1 id="Restaurant_title">Restaurant Reservations</h1>
                    <p>Book your table at the best spots across Jamaica.</p>
                </div>
            </section>
            <!-- This is used to display restaurant information in a grid -->
            <section id="popular-restaurants" class="popular-restaurants" aria-labelledby="popular-restaurants-title">
                <div class="popular-restaurants-heading">
                    <div>
                        <h2 id="popular-restaurants-title">Popular Restaurants</h2>
                        <p>Discover some of Jamaica's favourite places to dine.</p>
                    </div>
                </div>

                <div class="restaurant-grid"> 
                    <article class="restaurant-card">
                        <img class="restaurant-photo restaurant-photo-one" src="RestaurantReservations.jpg" alt="Set table ready for a restaurant reservation">
                        <div class="restaurant-card-content">
                            <h3>The Jerk Centre</h3>
                            <p class="restaurant-cuisine">Parish . City</p>
                            <p class="restaurant-location">St. Ann, Ocho Rios </p>
                            <p class="restaurant-rating"><span aria-hidden="true">Rating</span> 0 <span class="rating-count">(0 reviews)</span></p>
                        </div>
                    </article>

                    <article class="restaurant-card">
                        <img class="restaurant-photo restaurant-photo-two" src="RestaurantReservations.jpg" >
                        <div class="restaurant-card-content">
                            <h3>Pier 1 Restaurant</h3>
                            <p class="restaurant-cuisine">Parish . City</p>
                            <p class="restaurant-location">Montego Bay, Howard Cooke Boulevard</p>
                            <p class="restaurant-rating"><span aria-hidden="true">Rating</span> 0 <span class="rating-count">(0 reviews)</span></p>
                        </div>
                    </article>

                    <article class="restaurant-card">
                        <img class="restaurant-photo restaurant-photo-three" src="RestaurantReservations.jpg" alt="Restaurant table set for dinner">
                        <div class="restaurant-card-content">
                            <h3>Food to go</h3>
                            <p class="restaurant-cuisine">Parish . City</p>
                            <p class="restaurant-location">St.Ann, Ocho Rios</p>
                            <p class="restaurant-rating"><span aria-hidden="true">Rating</span> 0 <span class="rating-count">(0 reviews)</span></p>
                        </div>
                    </article>

                    <article class="restaurant-card">
                        <img class="restaurant-photo restaurant-photo-four" src="RestaurantReservations.jpg" alt="Warmly lit restaurant prepared for guests">
                        <div class="restaurant-card-content">
                            <h3>Sally's at Sunset</h3>
                            <p class="restaurant-cuisine">Parish . City</p>
                            <p class="restaurant-location">Westmoreland, Negril</p>
                            <p class="restaurant-rating"><span aria-hidden="true">Rating</span> 0 <span class="rating-count">(0 reviews)</span></p>
                        </div>
                    </article>
                </div>
            </section>
            <!-- End of restaurant grid -->
        </main>

        <!-- footer with copyright and slogan-->    
        <footer class="site-footer">
            <p>Plan your day your way.</p>
            <p class="footer-small">&copy; 2026 KMK Reservations</p>
        </footer>
    </body>
</html>