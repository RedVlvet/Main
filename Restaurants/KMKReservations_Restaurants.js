

var form = document.getElementById("booking-form");
var started = false;   // true once the user types something
var sent = false;      // true once the form is submitted

// Pop-up confirmation 
if (document.body.dataset.done === "1") {
    alert("Your reservation has been made.");
    window.location.href = "KMKReservations_MyBookings.php";
}

//Tracks if the user has started filling out the form
form.addEventListener("input", function () {
    started = true;
});

form.addEventListener("submit", function () {
    sent = true;
});

//Warns the user if they click a nav link before finishing
var links = document.querySelectorAll(".navbar a");

links.forEach(function (link) {
    link.addEventListener("click", function (e) {
        if (started && !sent) {
            var leave = confirm("You have not completed your restaurant reservation. Are you sure you want to exit?");
            if (!leave) {
                e.preventDefault();   // user clicked "No" / Cancel: stay on the page
            }
        }
    });
});