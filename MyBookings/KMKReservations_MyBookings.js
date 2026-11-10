
// Wait until the page has finished loading.
document.addEventListener("DOMContentLoaded", function () {

    // Get the service buttons.
    const serviceButtons = document.querySelectorAll(".service-card");

    // Get the Current and Previous buttons.
    const statusButtons = document.querySelectorAll(".tab");

    // Get all reservation cards.
    const reservationCards = document.querySelectorAll(".reservation-card");

    // Get the reservation heading.
    const reservationTitle = document.getElementById("reservationTitle");

    // Get the message shown when there are no reservations.
    const noReservations = document.getElementById("noReservations");

    // If the My Bookings elements are not on the page, there is nothing to do, so stop here.
    if (!reservationTitle || !noReservations) {
        return;
    }

    // Default selections when the page opens.
    let selectedService = "restaurant";
    let selectedStatus = "current";


    // Names used for the reservation heading.
    const serviceNames = {
        restaurant: "Restaurant",
        room: "Room",
        driver: "Driver"
    };


    // Display the reservations based on the selected buttons.
    function displayReservations() {

        let found = 0;

        reservationCards.forEach(function (card) {

            // Get the service and status of each reservation.
            const service = card.dataset.service;
            const status = card.dataset.status;

            // Check whether the reservation matches both selections.
            if (service === selectedService && status === selectedStatus) {

                card.hidden = false;
                found++;

            } else {

                card.hidden = true;

            }

        });


        // Show a message if no matching reservations are found.
        noReservations.hidden = found > 0;


        // Update the heading.
        const statusName =
            selectedStatus === "current" ? "Current" : "Previous";

        reservationTitle.textContent =
            statusName + " " + serviceNames[selectedService] + " Reservations";

    }


    // Handle clicks on Restaurant, Room and Drivers.
    serviceButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            selectedService = button.dataset.service;

            // Highlight the selected service.
            serviceButtons.forEach(function (item) {

                item.classList.remove("active");

            });

            button.classList.add("active");

            displayReservations();

        });

    });


    // Handle clicks on Current and Previous Reservations.
    statusButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            selectedStatus = button.dataset.status;

            // Highlight the selected tab.
            statusButtons.forEach(function (item) {

                item.classList.remove("active");

            });

            button.classList.add("active");

            displayReservations();

        });

    });


    // Display the default reservations when the page opens.
    displayReservations();

});
