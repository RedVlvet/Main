<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> KMK Reservation Home Page </title>
    <link rel="stylesheet" href="KMKReservations_Index.css">
</head>
<body>

    <!-- Navigation of the website-->
        <nav>
            <div class="navbar">
                <img src="KMKReservations_Logo.png" style="height:130px;width:130px">
                <a class = "active" href="KMKReservations_Index.php">Home</a>
                <a href="KMKReservations_Restaurants.php">Restaurant</a>
                <a href="KMKReservations_Rooms.php">Room</a>
                <a href="KMKReservations_Chauffeur.php">Drivers</a>
                <a href="KMKReservations_MyBookings.php">My Bookings</a>
            </div>
        </nav>
    
    <!-- Services -->
    <section class ="services-section">
        <h2> Choose your next step </h2>
        <h1> What would you like to reserve? </h1>

        <p> Select a service below to get started. </p>
                    
            <div class = "services-container">

                <!-- Restaurant -->
                <div class = "service-card">

                    <img src = "RestaurantReservation.jpg" style = "width: 250px; height: 150px"  alt = "Restaurant">
                
                    <div class = "service-content">
                        <h2> Restaurant Reservation </h2>
                        <a href = "KMKReservations_Restaurants.php" class = "explore-btn"> Explore Service </a>
                    </div>
            </div>    
            
            <!-- Rooms --> 
            <div class = "service-card">
                <img src ="RoomReservation.jpg" style="width: 250px; height: 150px" alt = "Room">

                <div class = "service-content">
                    <h2> Room Reservation </h2>
                    <a href = "KMKReservations_Rooms.php" class = "explore-btn"> Explore Service </a>
                </div>
            </div>

            <!-- Driver --> 
            <div class = "service-card">
                <img src = "DriverReservation.jpeg" style = "width: 250px; height: 150px" alt = "Driver">

                <div class = "service-content">
                    <h2> Driver Reservation </h2>
                    <a href = "KMKReservations_Chauffeur.php" class = "explore-btn"> Explore Service </a>
                </div>          
            </div>       
    </section>

   

    

    

    <!-- Draws a horizontal line at the bottom of the page --> 
    <hr> 

    <!--Footer-->
    <footer>
        <div id="content" style="text-align: center;">
            <p>&copy; KMK Reservations 2026 </p>
            <p>Plan your day your way.</p>
        </div>
    </footer>
</body>
</html>