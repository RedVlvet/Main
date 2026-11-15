<?php
    session_start();
    $isLoggedIn = isset($_SESSION['user_id']);

    $recentBookings = [];
    if ($isLoggedIn) {
        require_once '../Database/DB.php';
        require_once '../Shared/KMKReservations_Functions.php';

        $recentBookings = getAllRows($conn,
            "SELECT b.*, s.service_type, s.name AS service_name
             FROM bookings b
             JOIN services s ON b.service_id = s.service_id
             WHERE b.user_id = ?
             ORDER BY b.created_at DESC
             LIMIT 3",
            "i", [$_SESSION['user_id']]);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | KMK Reservations</title>
    <link rel="stylesheet" href="KMKReservations_Home.css">
</head>
<body>
    <header class="site-nav">
        <a href="../Home/KMKReservations_Home.php" class="nav-brand">
            <img src="../KMKReservations_Logo.png" alt="KMK Reservations logo">
        </a>
        <nav class="nav-links">
            <a class="active" href="../Home/KMKReservations_Home.php">Home</a>
            <a href="../Restaurants/KMKReservations_Restaurants.php">Restaurant</a>
            <a href="../Accomodations/KMKReservations_Accomodations.php">Accommodations</a>
            <a href="../Drivers/KMKReservations_Drivers.php">Drivers</a>
            <a href="../MyBookings/KMKReservations_MyBookings.php">My Bookings</a>
            <a href="../MyProfile/KMKReservations_MyProfile.php">My Profile</a>
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <a href="../Admin/KMKReservations_Admin.php">Admin</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <section class="home-banner">
            <div class="banner-text">
                <h1>Your plans, perfectly reserved.</h1>
                <p>Book restaurants, rooms and drivers all in one place.</p>
                <p>Fast, easy and reliable across Jamaica.</p>
                <a href="#services" class="banner-btn">Discover now</a>
            </div>
        </section>

        <?php if ($isLoggedIn): ?>
        <section class="recent-bookings">
            <h2>Your recent reservations</h2>

            <?php if (count($recentBookings) == 0): ?>
                <p class="recent-empty">You have no reservations yet. Choose a service below to get started.</p>
            <?php else: ?>
                <div class="recent-list">
                    <?php foreach ($recentBookings as $booking): ?>
                        <div class="recent-card">
                            <span class="recent-type"><?= serviceName($booking['service_type']) ?></span>
                            <h3><?= htmlspecialchars($booking['service_name']) ?></h3>
                            <p><?= htmlspecialchars($booking['booking_date']) ?> &middot; <?= htmlspecialchars($booking['reference']) ?></p>
                            <p class="recent-status"><?= htmlspecialchars($booking['status']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <a href="../MyBookings/KMKReservations_MyBookings.php" class="recent-link">See all my bookings &rarr;</a>
            <?php endif; ?>
        </section>
        <?php endif; ?>

        <section class="info">
            <section>
                <h2>About Us</h2>
                <p>
                    KMK Reservations is an online reservation and appointment booking platform designed to make planning easier and more convenient.
                    Our goal is to give customers one simple place to manage reservations for restaurants, hotels, and drivers.
                    We help users save time by making the booking process quick, organized, and accessible.
                </p>
            </section>

            <section>
                <h2>What We Do</h2>
                <ul>
                    <li>
                        <strong>Restaurant Reservations</strong>
                        <span>Book tables at restaurants based on your preferred date and time.</span>
                    </li>
                    <li>
                        <strong>Accommodations</strong>
                        <span>Find and reserve rooms for stays, whether for a short visit or longer trip.</span>
                    </li>
                    <li>
                        <strong>Driver Reservations</strong>
                        <span>Book a driver for transportation when you need a convenient way to get around.</span>
                    </li>
                    <li>
                        <strong>Booking Management</strong>
                        <span>Keep track of your reservations and appointment details in one place.</span>
                    </li>
                </ul>
            </section>
        </section>

        <section class="services-section" id="services">
            <div class="services-heading">
                <h1>What would you like to reserve?</h1>
                <p>Select a service below to get started.</p>
            </div>

            <div class="services-container">
                <div class="service-card restaurant-card">
                    <img class="service-image" src="../Restaurants/RestaurantReservation.jpg" alt="Restaurant reservation">
                    <div class="service-content">
                        <h2>Restaurant Reservation</h2>
                        <p>Find and reserve a table at your favourite restaurant.</p>
                        <a href="../Restaurants/KMKReservations_Restaurants.php" class="explore-btn">Explore Service</a>
                    </div>
                </div>

                <div class="service-card room-card">
                    <img class="service-image" src="../Accomodations/Accomodation.jpg" alt="Accommodation">
                    <div class="service-content">
                        <h2>Accommodations</h2>
                        <p>Find and reserve a comfortable space for your stay.</p>
                        <a href="../Accomodations/KMKReservations_Accomodations.php" class="explore-btn">Explore Service</a>
                    </div>
                </div>

                <div class="service-card driver-card">
                    <img class="service-image" src="../Drivers/DriverReservation.jpeg" alt="Driver reservation">
                    <div class="service-content">
                        <h2>Driver Reservation</h2>
                        <p>Book a reliable driver for your journey.</p>
                        <a href="../Drivers/KMKReservations_Drivers.php" class="explore-btn">Explore Service</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <p>Plan your day your way.</p>
        <p class="footer-small">&copy; 2026 KMK Reservations</p>
    </footer>
</body>
</html>