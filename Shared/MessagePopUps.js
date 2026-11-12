
// Shared function for displaying pop-up messages
function showMessage(
    title,
    message,
    icon,
    type,
    primaryText,
    primaryAction,
    secondaryText = "",
    secondaryAction = null
) {

    // Get the pop-up elements from the page
    const modal = document.getElementById("bookingModal");

    if (!modal) {
        console.error("Pop-up HTML was not found.");
        return;
    }

    const iconBox = document.getElementById("modalIcon");
    const titleBox = document.getElementById("modalTitle");
    const messageBox = document.getElementById("modalMessage");
    const primaryButton = document.getElementById("modalPrimary");
    const secondaryButton = document.getElementById("modalSecondary");

    // Update the pop-up information
    titleBox.textContent = title;
    messageBox.textContent = message;
    iconBox.textContent = icon;

    // Set the icon colour
    iconBox.className = "modal-icon " + type;

    // Set the main button
    primaryButton.textContent = primaryText;

    primaryButton.onclick = function () {
        closeMessage();

        if (typeof primaryAction === "function") {
            primaryAction();
        }
    };

    // Set the second button if one is needed
    if (secondaryText !== "") {

        secondaryButton.textContent = secondaryText;
        secondaryButton.classList.remove("hidden");

        secondaryButton.onclick = function () {
            closeMessage();

            if (typeof secondaryAction === "function") {
                secondaryAction();
            }
        };

    } else {
        secondaryButton.classList.add("hidden");
    }

    // Display the pop-up
    modal.classList.add("show");
}


document.addEventListener("DOMContentLoaded", function () {

    // Get the pop-up elements
    const closeButton = document.getElementById("modalClose");
    const modal = document.getElementById("bookingModal");

    // Close button functionality
    if (closeButton) {
        closeButton.addEventListener("click", closeMessage);
    }

    // Close when clicking outside the pop-up box
    if (modal) {
        modal.addEventListener("click", function (event) {
            if (event.target === modal) {
                closeMessage();
            }
        });
    }

    // PHP sets data-done="1" on the body after a booking passes the checks
    if (document.body.dataset.done === "1") {
        reservationSuccess();
    }

    // Get the booking form (Restaurants, Accomodations or Drivers page)
    const bookingForm = document.getElementById("bookingForm");

    if (bookingForm) {

        bookingForm.addEventListener("submit", function (event) {

            // Check whether the user is logged in
            const isLoggedIn = bookingForm.dataset.loggedIn === "true";

            if (!isLoggedIn) {

                // Stop the form and display the account-required pop-up
                event.preventDefault();
                accountRequired();

            }

            // If the user is logged in, the form is sent to PHP to be checked

        });

    }

});



// Function to close the pop-up
function closeMessage() {

    const modal = document.getElementById("bookingModal");

    if (modal) {
        modal.classList.remove("show");
    }
}


// 1. Account Required
function accountRequired() {

    showMessage(
        "Account Required",
        "Please create an account or sign in before making a reservation.",
        "!",
        "warning",
        "Go to My Profile",
        function () {
            window.location.href = "../MyProfile/KMKReservations_MyProfile.php";
        },
        "Maybe Later",
        null
    );
}


// 2. Reservation Successful
function reservationSuccess() {

    showMessage(
        "Booking Confirmed!",
        "Your reservation has been submitted successfully.",
        "✓",
        "success",
        "View My Bookings",
        function () {
            window.location.href = "../MyBookings/KMKReservations_MyBookings.php";
        },
        "Close",
        null
    );
}


// 3. Session About to Expire
function sessionWarning() {

    showMessage(
        "Are You Still There?",
        "Your session is about to expire. Would you like to continue?",
        "!",
        "warning",
        "Continue Session",
        function () {
            // Connect this button to your PHP session refresh later.
            window.location.reload();
        },
        "Log Out",
        function () {
            window.location.href = "../MyProfile/logout.php";
        }
    );
}


// 4. Leaving Before Completing a Reservation
function confirmExit(destination) {

    showMessage(
        "Wait! Don't Leave Yet",
        "You have not completed your reservation. Are you sure you want to leave?",
        "!",
        "warning",
        "Continue Booking",
        null,
        "Leave Page",
        function () {

            if (destination) {
                window.location.href = destination;
            }

        }
    );
}


// 5. Account Created Successfully
function accountCreated() {

    showMessage(
        "Account Created Successfully!",
        "Welcome to KMK Reservations! Your account has been created successfully.",
        "✓",
        "success",
        "Continue to My Profile",
        function () {
            window.location.href = "../MyProfile/KMKReservations_MyProfile.php";
        }
    );
}

