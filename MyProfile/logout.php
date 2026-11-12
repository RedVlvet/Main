<?php
    // Log the user out: clear the session and go back to the Home page
    session_start();

    $_SESSION = [];       // remove all session values
    session_destroy();    // end the session

    header("Location: ../Home/KMKReservations_Home.php");
    exit;
?>
