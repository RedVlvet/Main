<?php
// Start the session and checks the user is logged in.
    session_start();
    $isLoggedIn = isset($_SESSION['user_id']);


// After successfully inserting the new account
// and retrieving the new user's ID:

session_regenerate_id(true); //runs after successful sign in or when automatically signing in the user has an account

// Store the user's ID in the session


// Record the last activity time
$_SESSION['last_activity'] = time();

// Redirect to My Profile



// Temporary values until login/DB is connected
$fullName = $_SESSION['user_name']  ?? 'Guest User';
$email    = $_SESSION['user_email'] ?? 'guest@example.com';
$phone    = $_SESSION['user_phone'] ?? '';
$message  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: validate and save to the database with DB.php
    $message = 'Your details have been saved.';
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>My Account | KMK Reservations</title>
        <link rel="stylesheet" href="KMKReservations_MyProfile.css"> 
        <link rel="stylesheet" href="MessagePopUps.css">       
    </head>
    <body>

    <header class="site-nav">
        <a href="../Home/KMKReservations_Home.php" class="nav-brand">
            <img src="../KMKReservations_Logo.png" alt="KMK Reservations logo">
        </a>
        <nav class="nav-links">
            <a href="../Home/KMKReservations_Home.php">Home</a>
            <a href="../Restaurants/KMKReservations_Restaurants.php">Restaurant</a>
            <a href="../Accomodations/KMKReservations_Accomodations.php">Accomodations</a>
            <a href="../Drivers/KMKReservations_Drivers.php">Drivers</a>
            <a href="../MyBookings/KMKReservations_MyBookings.php">My Bookings</a>
            <a class ="active" href="../MyProfile/KMKReservations_MyProfile.php">My Profile</a>
        </nav>
    </header>

    <section class="page-hero">
        <div class="hero-icon">&#128100;</div>
        <div>
            <h1>My Account</h1>
            <p>Manage your details and keep your bookings up to date.</p>
        </div>
    </section>

    <main class="account-layout">

        <aside class="profile-card">
            <div class="avatar"><?= strtoupper(substr($fullName, 0, 1)) ?></div>
            <h2><?= htmlspecialchars($fullName) ?></h2>
            <p class="profile-email"><?= htmlspecialchars($email) ?></p>
            <a href="../MyBookings/KMKReservations_MyBookings.php" class="btn btn-primary">View my bookings</a>
            <a href="logout.php" class="btn btn-outline">Log out</a>
        </aside>

        <section class="form-card">
            <h2>Personal details</h2>

            <?php if ($message): ?>
                <p class="alert-success"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <form method="post" action="">
                <div class="field">
                    <label for="name">Full name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($fullName) ?>" required>
                </div>

                <div class="field">
                    <label for="email">Email address</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                </div>

                <div class="field">
                    <label for="phone">Phone number</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>" placeholder="876-000-0000">
                </div>

                <h2 class="form-subtitle">Change password</h2>

                <div class="field">
                    <label for="new_password">New password</label>
                    <input type="password" id="new_password" name="new_password" autocomplete="new-password">
                </div>

                <div class="field">
                    <label for="confirm_password">Confirm new password</label>
                    <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password">
                </div>

                <button type="submit" class="btn btn-primary">Save changes</button>
            </form>
        </section>
    </main>

        <!-- Link the JavaScript file -->
        <script> src="MessagePopUps.js"></script>
        <script src="../Myb/ookings/KMKReservations_MyBookings.js"></script>
        <script src ="../MyProfile/KMKReservations_MyProfile.js"></script>

    <footer class="site-footer">
        <p>Plan your day your way.</p>
        <p class="footer-small">&copy; 2026 KMK Reservations</p>
    </footer>
    </body>
</html>
