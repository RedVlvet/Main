
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

    titleBox.textContent = title;
    messageBox.textContent = message;
    iconBox.textContent = icon;

    iconBox.className = "modal-icon " + type;

    primaryButton.textContent = primaryText;

    primaryButton.onclick = function () {
        closeMessage();

        if (typeof primaryAction === "function") {
            primaryAction();
        }
    };

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

    modal.classList.add("show");
}


document.addEventListener("DOMContentLoaded", function () {

    const closeButton = document.getElementById("modalClose");
    const modal = document.getElementById("bookingModal");

    if (closeButton) {
        closeButton.addEventListener("click", closeMessage);
    }

    if (modal) {
        modal.addEventListener("click", function (event) {
            if (event.target === modal) {
                closeMessage();
            }
        });
    }

});



function closeMessage() {

    const modal = document.getElementById("bookingModal");

    if (modal) {
        modal.classList.remove("show");
    }
}


function accountRequired() {

    showMessage(
        "Account Required",
        "Please create an account or sign in before making a reservation.",
        "!",
        "warning",
        "Go to My Profile",
        function () {
            window.location.href = "MyProfile.php";
        },
        "Maybe Later",
        null
    );
}


function reservationSuccess() {

    showMessage(
        "Booking Confirmed!",
        "Your reservation has been submitted successfully.",
        "✓",
        "success",
        "View My Bookings",
        function () {
            window.location.href = "MyBookings.php";
        },
        "Close",
        null
    );
}


function sessionWarning() {

    showMessage(
        "Are You Still There?",
        "Your session is about to expire. Would you like to continue?",
        "!",
        "warning",
        "Continue Session",
        function () {
            window.location.reload();
        },
        "Log Out",
        function () {
            window.location.href = "MyProfile.php?logout=1";
        }
    );
}


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


function accountCreated() {

    showMessage(
        "Account Created Successfully!",
        "Welcome to KMK Reservations! Your account has been created successfully.",
        "✓",
        "success",
        "Continue to My Profile",
        function () {
            window.location.href = "MyProfile.php";
        }
    );
}


document.addEventListener("DOMContentLoaded", function () {

    const closeButton = document.getElementById("modalClose");
    const modal = document.getElementById("bookingModal");

    if (closeButton) {
        closeButton.addEventListener("click", closeMessage);
    }

    if (modal) {

        modal.addEventListener("click", function (event) {

            if (event.target === modal) {
                closeMessage();
            }

        });
    }

});
