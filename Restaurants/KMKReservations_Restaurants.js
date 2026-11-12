

var form = document.getElementById("bookingForm");
var started = false;   // true once the user types something
var sent = false;      // true once the form is submitted

// Pop-up confirmation is now shown by reservationSuccess() in MessagePopUps.js

//Tracks if the user has started filling out the form
form.addEventListener("input", function () {
    started = true;
});

form.addEventListener("submit", function () {
    sent = true;
});

//Warns the user if they click a nav link before finishing
var links = document.querySelectorAll(".nav-links a");

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