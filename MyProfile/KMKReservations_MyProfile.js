// Check if the account was successfully created
const pageParameters = new URLSearchParams(window.location.search);

if (pageParameters.get("account") === "created") {

    accountCreated();

}