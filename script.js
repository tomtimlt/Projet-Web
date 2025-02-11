document.addEventListener("DOMContentLoaded", function() {
    
    /* Variable en JS */
    
    const cookieNotification = document.getElementById("cookie-notification");
    const acceptButton = document.getElementById("accept-cookies");
    const declineButton = document.getElementById("decline-cookies");
    const cookiePolicy = document.getElementById("cookie-policy");
    const validateButton = document.getElementById("validation");

    /* Fonction en JS */

    if (localStorage.getItem("cookiesAccepted") === "true") {
       cookieNotification.style.display = "block"; // mets none pour que la fonction marche 
    } else {
        cookieNotification.style.display = "block";
    }

    acceptButton.addEventListener("click", function() {
        localStorage.setItem("cookiesAccepted", "true");
        cookieNotification.style.display = "none";
    });

    declineButton.addEventListener("click", function() {
    cookieNotification.style.display = "none"
    cookiePolicy.style.display = "block";
    });

    validateButton.addEventListener("click", function() {
        cookiePolicy.style.display = "none";
    });
    
});