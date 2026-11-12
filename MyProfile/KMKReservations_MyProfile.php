<?php
// Start the session and checks the user is logged in.
    session_start();
    $isLoggedIn = isset($_SESSION['user_id']);

    // Database connection and shared functions
    require_once '../Database/DB.php';
    require_once '../Shared/KMKReservations_Functions.php';

    $errors  = [];   // list of error messages to show
    $message = '';   // success message to show

    // Values kept in the forms if there is an error
    $loginEmail  = '';
    $signupName  = '';
    $signupEmail = '';
    $signupPhone = '';

    // Details of the logged in user (from the session for now)
    $fullName = $_SESSION['user_name']  ?? '';
    $email    = $_SESSION['user_email'] ?? '';
    $phone    = $_SESSION['user_phone'] ?? '';

    // Logs the user in: saves who they are in the session, then goes to the right page
    function logUserIn($user, $newAccount = false) {
        session_regenerate_id(true);   // new session id after log in, so the old one can't be reused

        $_SESSION['user_id']       = $user['user_id'];
        $_SESSION['user_name']     = $user['full_name'];
        $_SESSION['user_email']    = $user['email'];
        $_SESSION['user_phone']    = $user['phone'];
        $_SESSION['role']          = $user['role'];
        $_SESSION['last_activity'] = time();

        if ($user['role'] === 'admin') {
            header("Location: ../Admin/KMKReservations_Admin.php");
        } elseif ($newAccount) {
            header("Location: ../MyProfile/KMKReservations_MyProfile.php?account=created");
        } else {
            header("Location: ../MyProfile/KMKReservations_MyProfile.php");
        }
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Each form has a hidden "action" field so we know which one was sent
        $action = $_POST['action'] ?? '';

        // ---------- 1. LOG IN ----------
        if ($action === 'login') {
            $loginEmail = trim($_POST['email'] ?? '');
            $password   = $_POST['password'] ?? '';

            if ($loginEmail == '' || $password == '') {
                $errors[] = 'Please enter your email and password.';
            } elseif (!filter_var($loginEmail, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Please enter a valid email address.';
            } else {
                // Find the user with this email (prepared statement)
                $user = getOneRow($conn, "SELECT * FROM users WHERE email = ?", "s", [$loginEmail]);

                // password_verify checks what they typed against the saved hash
                if ($user && password_verify($password, $user['password'])) {
                    logUserIn($user);
                } else {
                    // Same message for a wrong email or a wrong password, so nobody can guess who has an account
                    $errors[] = 'Incorrect email or password.';
                }
            }
        }

        // ---------- 2. CREATE ACCOUNT ----------
        if ($action === 'signup') {
            $signupName  = trim($_POST['name'] ?? '');
            $signupEmail = trim($_POST['email'] ?? '');
            $signupPhone = trim($_POST['phone'] ?? '');
            $password    = $_POST['password'] ?? '';
            $confirm     = $_POST['confirm_password'] ?? '';

            if ($signupName == '' || $signupEmail == '' || $signupPhone == '' || $password == '') {
                $errors[] = 'All fields are required.';
            }
            if ($signupEmail != '' && !filter_var($signupEmail, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Please enter a valid email address.';
            }
            if ($signupPhone != '' && !isValidPhone($signupPhone)) {
                $errors[] = 'Please enter a valid phone number.';
            }
            checkPassword($password, $confirm, $errors);

            // The email can only be used for one account
            if (count($errors) == 0 && getOneRow($conn, "SELECT user_id FROM users WHERE email = ?", "s", [$signupEmail])) {
                $errors[] = 'An account with this email already exists. Please log in instead.';
            }

            if (count($errors) == 0) {
                // Never save the real password, only the hash
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // New accounts are always regular users (admins are added by KMK staff)
                $saved = runQuery($conn,
                    "INSERT INTO users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, 'user')",
                    "ssss", [$signupName, $signupEmail, $signupPhone, $hashedPassword]);

                if ($saved) {
                    $user = getOneRow($conn, "SELECT * FROM users WHERE user_id = ?", "i", [mysqli_insert_id($conn)]);
                    logUserIn($user, true);
                } else {
                    $errors[] = 'Sorry, your account could not be created. Please try again.';
                }
            }
        }


        // ---------- 3. UPDATE PERSONAL DETAILS (logged in users only) ----------
        if ($action === 'update' && $isLoggedIn) {
            $fullName    = trim($_POST['name'] ?? '');
            $email       = trim($_POST['email'] ?? '');
            $phone       = trim($_POST['phone'] ?? '');
            $newPassword = $_POST['new_password'] ?? '';
            $confirm     = $_POST['confirm_password'] ?? '';

            if ($fullName == '' || $email == '') {
                $errors[] = 'Full name and email are required.';
            }
            if ($email != '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Please enter a valid email address.';
            }
            if ($phone != '' && !isValidPhone($phone)) {
                $errors[] = 'Please enter a valid phone number.';
            }
            // Only check the password if the user typed a new one
            if ($newPassword != '' || $confirm != '') {
                checkPassword($newPassword, $confirm, $errors);
            }

            // The new email can't belong to someone else
            if (count($errors) == 0 && getOneRow($conn, "SELECT user_id FROM users WHERE email = ? AND user_id <> ?",
                                                 "si", [$email, $_SESSION['user_id']])) {
                $errors[] = 'This email is already used by another account.';
            }

            if (count($errors) == 0) {
                // Save the new details in the users table
                runQuery($conn, "UPDATE users SET full_name = ?, email = ?, phone = ? WHERE user_id = ?",
                         "sssi", [$fullName, $email, $phone, $_SESSION['user_id']]);

                // Only change the password if a new one was typed
                if ($newPassword != '') {
                    runQuery($conn, "UPDATE users SET password = ? WHERE user_id = ?",
                             "si", [password_hash($newPassword, PASSWORD_DEFAULT), $_SESSION['user_id']]);
                }

                // Keep the new details in the session so the page shows them
                $_SESSION['user_name']  = $fullName;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_phone'] = $phone;

                $message = 'Your details have been updated.';
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>My Account | KMK Reservations</title>
        <link rel="stylesheet" href="KMKReservations_MyProfile.css"> 
        <link rel="stylesheet" href="../Shared/MessagePopUps.css">       
    </head>
    <body>

    <header class="site-nav">
        <a href="../Home/KMKReservations_Home.php" class="nav-brand">
            <img src="../KMKReservations_Logo.png" alt="KMK Reservations logo">
        </a>
        <nav class="nav-links">
            <a href="../Home/KMKReservations_Home.php">Home</a>
            <a href="../Restaurants/KMKReservations_Restaurants.php">Restaurant</a>
            <a href="../Accomodations/KMKReservations_Accomodations.php">Accommodations</a>
            <a href="../Drivers/KMKReservations_Drivers.php">Drivers</a>
            <a href="../MyBookings/KMKReservations_MyBookings.php">My Bookings</a>
            <a class ="active" href="../MyProfile/KMKReservations_MyProfile.php">My Profile</a>
            <!-- Admin link: only admins see it -->
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <a href="../Admin/KMKReservations_Admin.php">Admin</a>
            <?php endif; ?>
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

    <?php if (!$isLoggedIn): ?>

        <!-- ===== Not logged in: Log in and Create account ===== -->
        <section class="form-card">
            <h2>Log in</h2>

            <?php if (count($errors) > 0 && ($_POST['action'] ?? '') === 'login'): ?>
                <div class="alert-error">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <input type="hidden" name="action" value="login">

                <div class="field">
                    <label for="login_email">Email address</label>
                    <input type="email" id="login_email" name="email" value="<?= htmlspecialchars($loginEmail) ?>" required>
                </div>

                <div class="field">
                    <label for="login_password">Password</label>
                    <input type="password" id="login_password" name="password" autocomplete="current-password" required>
                </div>

                <button type="submit" class="btn btn-primary">Log in</button>
            </form>
        </section>

        <section class="form-card">
            <h2>Create an account</h2>

            <?php if (count($errors) > 0 && ($_POST['action'] ?? '') === 'signup'): ?>
                <div class="alert-error">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <input type="hidden" name="action" value="signup">

                <div class="field">
                    <label for="signup_name">Full name</label>
                    <input type="text" id="signup_name" name="name" value="<?= htmlspecialchars($signupName) ?>" required>
                </div>

                <div class="field">
                    <label for="signup_email">Email address</label>
                    <input type="email" id="signup_email" name="email" value="<?= htmlspecialchars($signupEmail) ?>" required>
                </div>

                <div class="field">
                    <label for="signup_phone">Phone number</label>
                    <input type="tel" id="signup_phone" name="phone" value="<?= htmlspecialchars($signupPhone) ?>" placeholder="876-000-0000" required>
                </div>

                <div class="field">
                    <label for="signup_password">Password (at least 8 characters)</label>
                    <input type="password" id="signup_password" name="password" autocomplete="new-password" required>
                </div>

                <div class="field">
                    <label for="signup_confirm">Confirm password</label>
                    <input type="password" id="signup_confirm" name="confirm_password" autocomplete="new-password" required>
                </div>

                <button type="submit" class="btn btn-primary">Create account</button>
            </form>
        </section>

    <?php else: ?>

        <!-- ===== Logged in: profile and personal details ===== -->
        <aside class="profile-card">
            <div class="avatar"><?= htmlspecialchars(strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1))) ?></div>
            <h2><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></h2>
            <p class="profile-email"><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></p>
            <a href="../MyBookings/KMKReservations_MyBookings.php" class="btn btn-primary">View my bookings</a>
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <a href="../Admin/KMKReservations_Admin.php" class="btn btn-primary">Admin dashboard</a>
            <?php endif; ?>
            <a href="logout.php" class="btn btn-outline">Log out</a>
        </aside>

        <section class="form-card">
            <h2>Personal details</h2>

            <?php if ($message): ?>
                <p class="alert-success"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <?php if (count($errors) > 0): ?>
                <div class="alert-error">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <input type="hidden" name="action" value="update">

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

    <?php endif; ?>

    </main>


        <!-- Pop-up message box (used by MessagePopUps.js) -->
        <div id="bookingModal" class="modal-overlay">
            <div class="modal-box">
                <button
                    type="button"
                    id="modalClose"
                    class="close-btn"
                    aria-label="Close pop-up">
                    &times;
                </button>

                <div id="modalIcon" class="modal-icon">
                    !
                </div>

                <h2 id="modalTitle" class="modal-title">
                    Message Title
                </h2>

                <p id="modalMessage" class="modal-message">
                    Message will appear here.
                </p>

                <div class="modal-actions">

                    <button
                        type="button"
                        id="modalPrimary"
                        class="modal-button">
                        Continue
                    </button>

                    <button
                        type="button"
                        id="modalSecondary"
                        class="modal-button secondary">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- Link the JavaScript file -->
        <script src="../Shared/MessagePopUps.js"></script>
        <script src ="../MyProfile/KMKReservations_MyProfile.js"></script>

    <footer class="site-footer">
        <p>Plan your day your way.</p>
        <p class="footer-small">&copy; 2026 KMK Reservations</p>
    </footer>
    </body>
</html>
