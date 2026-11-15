
document.addEventListener("DOMContentLoaded", function () {

    const serviceButtons = document.querySelectorAll(".service-card");

    const statusButtons = document.querySelectorAll(".tab");

    const reservationCards = document.querySelectorAll(".reservation-card");

    const reservationTitle = document.getElementById("reservationTitle");

    const noReservations = document.getElementById("noReservations");

    if (!reservationTitle || !noReservations) {
        return;
    }

    let selectedService = "restaurant";
    let selectedStatus = "current";


    const serviceNames = {
        restaurant: "Restaurant",
        room: "Room",
        driver: "Driver"
    };


    function displayReservations() {

        let found = 0;

        reservationCards.forEach(function (card) {

            const service = card.dataset.service;
            const status = card.dataset.status;

            if (service === selectedService && status === selectedStatus) {

                card.hidden = false;
                found++;
            } else {
                card.hidden = true;
            }
        });

        noReservations.hidden = found > 0;

        const statusName =
            selectedStatus === "current" ? "Current" : "Previous";

        reservationTitle.textContent =
            statusName + " " + serviceNames[selectedService] + " Reservations";

    }


    serviceButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            selectedService = button.dataset.service;

            serviceButtons.forEach(function (item) {

                item.classList.remove("active");
            });

            button.classList.add("active");

            displayReservations();

        });

    });


    statusButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            selectedStatus = button.dataset.status;

            statusButtons.forEach(function (item) {

                item.classList.remove("active");
            });

            button.classList.add("active");

            displayReservations();

        });

    });


    displayReservations();

});
