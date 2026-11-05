<?php
    session_start();
    // require_once 'DB.php';   
    $basepath = dirname(__DIR__);
    $base = str_replace('\\','/',$basepath);

    $name = $_SESSION['user_name'] ?? '';
    $restaurant = '';
    $date = $time = $guests = '';
    $error = '';
    $done  = false;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name       = trim($_POST['name']);
        $restaurant = trim($_POST['restaurant']);
        $date       = $_POST['date'];
        $time       = $_POST['time'];
        $guests     = $_POST['guests'];
    
        if ($name == '' || $restaurant == '' || $date == '' || $time == '' || $guests == '') {
            $error = 'All fields are required.';
        } elseif ($date < date('Y-m-d')) {
            $error = 'The date cannot be in the past.';
        } else {
            // Save to the database with DB.php
            $done = true;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Restaurants | KMK Reservations</title>
        <link rel="stylesheet" href="KMKReservations_Restaurants.css">    
    </head>

    <!-- data-done lets javascript know if the form was submitted successfully; 
    1 being yes 0 being no  using ternary operatior --> 
    <body data-done="<?= $done ? '1' : '0' ?>">       
        <nav>
            <div class="navbar">
                <img src="../KMKReservations_Logo.png" style="width:150px">
                        <a href="../KMKReservations_Home.php">Home</a>
                        <a class ="active" href="../Restaurants/KMKReservations_Restaurants.php">Restaurant</a>
                        <a href="../Rooms/KMKReservations_Rooms.php">Rooms</a>
                        <a href="../Drivers/KMKReservations_Chauffeur.php">Drivers</a>
                        <a href="../MyBookings/KMKReservations_MyBookings.php">My Bookings</a>
                        <a href="../MyProfile/KMKReservations_MyProfile.php" aria-label="MyProfile">&#128100;</a>
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

        <main class = "restaurants-layout">
            <!-- restaurant reservation form -->
             <section class ="form-card" id ="reservation-form">
                <h2> Reserve your table </h2>

                <!-- Only shows a message if there is an error in the form -->
                <?php if ($error) : ?>
                    <p class="alert-error"> <?=htmlspecialchars($error) ?> </p>
                <?php endif; ?>

                <!-- sends the data back to the same page for it to be processed -->
                 <form method = "post" action = "" id="booking-form">

                 <!--Where the client enters their name-->
                <div class = "field">
                    <label for = "name"> Name of the client </label>
                    <input type ="text" id="name" name="name" placeholder="Mary Jane" required> <!-- place after name='name' value="<?=htmlspecialchars($name) ?>" --> 
                </div>

                <!--Where the client enters the name of the restaurant 
                <div class = "field">
                    <label for="restaurant"> Name of the restaurant </label>
                    <input type="text" id="restaurant" name="restaurant" placeholder = "Hong Kong Restaurant" required>
                    <place after name='name' value="<?=htmlspecialchars($name) ?>" 
                </div>
                -->

            <!--Where the client selects the name of the restaurant -->
            <div class = "field">
                <label for = "restaurant"> Select restaurant: </label>
                    <select id = "restaurant" name = "restaurant" required>
                        <option value = ""> Select a restaurant </option>
                        <option value = "The Jerk Centre" <?= $restaurant === 'The Jerk Centre' ? 'selected' : '' ?>>The Jerk Centre - Ocho Rios</option>
                        <option value = "Pier 1 Restaurant" <?= $restaurant === 'Pier 1 Restaurant' ? 'selected' : '' ?>>Pier 1 Restaurant -Howard Cooke Boulevard</option>
                        <option value = "Food to go" <?= $restaurant === 'Food to go' ? 'selected' : '' ?>>Food to go - Ocho Rios</option>
                        <option value = "Sally's at Sunset" <?= $restaurant === "Sally's at Sunset" ? 'selected' : '' ?>>Sally's at Sunset - Negril</option>
                    </select>
                </div><br>


                <!--Where the client enters the date of the reservation-->
                <div class = "field">
                    <label for="date"> Date of the reservation </label>

                    <!-- min stops the user from picking a date that has already passed -->
                    <input type = "date" id="date" name="date" value="<?= htmlspecialchars($date) ?> " min =" <?= date ('Y-m-d') ?>" required>
                </div>

                <!--Where the client enters the time of the reservation-->
                <div class = "field">
                    <label for = "time"> Time for your reservation </label>
                    <input type = "time" id="time" name = "time" value = "<?= htmlspecialchars($time) ?>" required>
                </div>

                <!--Where the client enters the number of persons-->
                <div class = "field">
                    <label for = "guests"> Number of Persons: </label>
                    <select id = "guests" name = "guests" value = "<?=htmlspecialchars($guests) ?>" required>
                        <option value = ""> Select number of persons </option>
                        <option value = "1"> 1 </option>
                        <option value = "2"> 2 </option>
                        <option value = "3"> 3 </option>
                        <option value = "4"> 4 </option>
                        <option value = "5"> 5 </option>
                        <option value = "6"> 6 </option>
                        <option value = "7"> 7 </option>
                        <option value = "8"> 8 </option>
                    </select>
                </div><br>

                <button type = "submit" id = "submit-button"> Reserve </button>

                </form> 
            </section>           
        </main>
        
        <!-- handles events/actions in the restaurant reservation form -->
        <script src="KMKReservations_Restaurants.js"></script>
        <hr> 

        <!-- footer with copyright and slogan-->    
        <footer class="site-footer">
            <p>Plan your day your way.</p>
            <p class="footer-small">&copy; 2026 KMK Reservations</p>
        </footer>
    </body>
</html>