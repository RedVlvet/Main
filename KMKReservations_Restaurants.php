<?php
    session_start(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewCt" content="width=device-width, initial-scale=1.0">
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
                <a href="KMKReservations_Account.php" class="nav-account" >&#128100;</a>
            </nav>
        </header>
         <!-- page banner -->
        <main class="main">
            <section id="R" class="Restaurant_Banner" >
                
                <div >
                    <h1 id="Restaurant_title">Restaurant Reservations</h1>
                    <p>Book your table at the best spots across Jamaica.</p>
                </div>
            </section>
            <!-- This is used to display R information in a grid -->
            <section id="popular-restaurants"  >
                <div class="heading">
                    <div>
                        <h2>Popular Restaurants</h2>
                        <p>Discover some of Jamaica's favourite places to dine.</p>
                    </div>
                </div>

                <div class="grid"> 
                    <article class="Restaurant">
                        <img class="Restaurant_photo" src="RestaurantReservations.jpg" >
                        <div class="content">
                            <h3>The Jerk Centre</h3>
                            <p class="R-cuisine">Parish . City</p>
                            <p class="R">St. Ann, Ocho Rios </p>
                            <p class="R-rating"><span aria-hidden="true">Rating</span> 0 <span class="rating-count">(0 reviews)</span></p>
                        </div>
                    </article>

                    <article class="Restaurant">
                        <img class="Restaurant_photo" src="RestaurantReservations.jpg" >
                        <div class="Card_content">
                            <h3>Pier 1 Restaurant</h3>
                            <p class="R-cuisine">Parish . City</p>
                            <p class="location">Montego Bay, Howard Cooke Boulevard</p>
                            <p class="rating"><span aria-hidden="true">Rating</span> 0 <span class="rating-count">(0 reviews)</span></p>
                        </div>
                    </article>

                    <article class="Restaurant">
                        <img  src="RestaurantReservations.jpg" >
                        <div class="Card_content">
                            <h3>Food to go</h3>
                            <p class="R-cuisine">Parish . City</p>
                            <p class="location">St.Ann, Ocho Rios</p>
                            <p class="rating"><span aria-hidden="true">Rating</span> 0 <span class="rating-count">(0 reviews)</span></p>
                        </div>
                    </article>

                    <article class="Restaurant">
                        <img class="Restaurant_photo " src="RestaurantReservations.jpg" >
                        <div class="Card_content">
                            <h3>Sally's at Sunset</h3>
                            <p class="">Parish . City</p>
                            <p class="location">Westmoreland, Negril</p>
                            <p class="rating"><span aria-hidden="true">Rating</span> 0 <span class="rating-count">(0 reviews)</span></p>
                        </div>
                    </article>
                </div>
            </section>
            <!-- End of R grid -->
        </main>

        <!-- footer with copyright and slogan-->    
        <footer class="site-footer">
            <p>Plan your day your way.</p>
            <p class="footer">&copy; 2026 KMK Reservations</p>
        </footer>
    </body>
</html>