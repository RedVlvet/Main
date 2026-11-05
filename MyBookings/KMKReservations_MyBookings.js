
// Get the booking form and pop-up elements
const bookingForm = document.getElementById("bookingForm");

const accountModal = document.getElementById("accountModal");

const closeModal = document.getElementById("closeModal");

const cancelBtn = document.getElementById("cancelBtn");

const profileBtn = document.getElementById("profileBtn");


// Only run this code if the page has a booking form
if (bookingForm) {

    // Check when the user submits the booking form
    bookingForm.addEventListener("submit", function(event) {

        // Get the login status from PHP
        const loggedIn = bookingForm.dataset.loggedIn === "true";

        // Get the profile page link
        const profilePage = bookingForm.dataset.profileUrl;

        // Stop the booking if the user is not logged in
        if (!loggedIn) {

            event.preventDefault();

            // Display the account required pop-up
            accountModal.classList.add("show");

        } else {

            // Check that the check-out date is after check-in
            if (bookingForm.dataset.service === "rooms") {

                const checkin = document.getElementById("checkin").value;
                const checkout = document.getElementById("checkout").value;

                if (new Date(checkout) <= new Date(checkin)) {

                    event.preventDefault();

                    alert("Check-out date must be after check-in date.");

                    return;
                }
            }

            // The form can now continue to process-booking.php
        }
    });


    // Redirect the user to My Profile
    profileBtn.addEventListener("click", function() {

        window.location.href = bookingForm.dataset.profileUrl;

    });


    // Close the pop-up using the X button
    closeModal.addEventListener("click", function() {

        accountModal.classList.remove("show");

    });


    // Close the pop-up using Maybe Later
    cancelBtn.addEventListener("click", function() {

        accountModal.classList.remove("show");

    });


    // Close the pop-up when the user clicks outside the box
    accountModal.addEventListener("click", function(event) {

        if (event.target === accountModal) {

            accountModal.classList.remove("show");

        }

    });

}


// Set the earliest check-in date to today
const checkinField = document.getElementById("checkin");

const checkoutField = document.getElementById("checkout");

if (checkinField && checkoutField) {

    // Get today's date in YYYY-MM-DD format
    const today = new Date();
    const localToday = new Date(
        today.getTime() - today.getTimezoneOffset() * 60000
    ).toISOString().split("T")[0];

    checkinField.min = localToday;

    // Update the earliest checkout date when check-in changes
    checkinField.addEventListener("change", function() {

        checkoutField.min = checkinField.value;

        // Clear an invalid checkout date
        if (checkoutField.value &&
            checkoutField.value <= checkinField.value) {

            checkoutField.value = "";
        }

    });

}
