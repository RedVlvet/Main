
// Drivers page: extra form features (inspired by the Knutsford Express booking form)
document.addEventListener("DOMContentLoaded", function () {

    const fromSelect = document.getElementById("from");
    const toSelect = document.getElementById("to");
    const swapButton = document.getElementById("swapBtn");
    const returnRow = document.getElementById("returnRow");
    const tripRadios = document.querySelectorAll('input[name="trip_type"]');
    const passengerButton = document.getElementById("passengerBtn");
    const passengerBox = document.getElementById("passengerBox");
    const passengerSummary = document.getElementById("passengerSummary");
    const adults = document.getElementById("adults");
    const children = document.getElementById("children");
    const bookingForm = document.getElementById("bookingForm");


    // 1. Swap "Travelling from" and "Travelling to"
    swapButton.addEventListener("click", function () {
        const temp = fromSelect.value;
        fromSelect.value = toSelect.value;
        toSelect.value = temp;
    });


    // 2. Show the return date and time only for a Return trip
    function updateTripType() {
        const isReturn = document.querySelector('input[name="trip_type"]:checked').value === "return";
        returnRow.hidden = !isReturn;
        document.getElementById("return_date").required = isReturn;
        document.getElementById("return_time").required = isReturn;
    }

    tripRadios.forEach(function (radio) {
        radio.addEventListener("change", updateTripType);
    });

    updateTripType();


    // 3. Passenger picker: shows a short summary, click to open the choices
    function updateSummary() {
        const a = adults.value;
        const c = children.value;
        let text = a + (a === "1" ? " Adult" : " Adults");
        text += c === "0" ? ", No Children" : ", " + c + (c === "1" ? " Child" : " Children");
        passengerSummary.textContent = text;
    }

    passengerBox.hidden = true;   // closed until the user clicks

    passengerButton.addEventListener("click", function () {
        passengerBox.hidden = !passengerBox.hidden;
        passengerButton.setAttribute("aria-expanded", passengerBox.hidden ? "false" : "true");
    });

    adults.addEventListener("change", updateSummary);
    children.addEventListener("change", updateSummary);
    updateSummary();


    // 4. Error pop-up if from and to are the same place
    bookingForm.addEventListener("submit", function (event) {
        if (bookingForm.dataset.loggedIn === "true" && fromSelect.value !== "" && fromSelect.value === toSelect.value) {
            event.preventDefault();
            showMessage(
                "Error",
                "Travelling from and travelling to cannot be the same place.",
                "!",
                "error",
                "OK",
                closeMessage
            );
        }
    });

});
