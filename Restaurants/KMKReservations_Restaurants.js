

var form = document.getElementById("bookingForm");
var started = false;
var sent = false;

form.addEventListener("input", function () {
    started = true;
});

form.addEventListener("submit", function () {
    sent = true;
});

var links = document.querySelectorAll(".nav.links a");

links.forEach(function (link) {
    link.addEventListener("click", function (e) {
        if (started && !sent) {
            var leave = confirm("You have not completed your restaurant reservation. Are you sure you want to exit?");
            if (!leave) {
                e.preventDefault();
            }
        }
    });
});