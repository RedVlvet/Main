const pageParameters = new URLSearchParams(window.location.search);

if (pageParameters.get("account") === "created") {
    accountCreated();
}