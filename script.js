document.addEventListener("DOMContentLoaded", function() {
    
    /* Variable en JS */
    
    const cookieNotification = document.getElementById("cookie-notification");
    const acceptButton = document.getElementById("accept-cookies");
    const declineButton = document.getElementById("decline-cookies");
    const cookiePolicy = document.getElementById("cookie-policy");
    const validateButton = document.getElementById("validation");
    const navbar = document.querySelector('.navbar');
    let lastScroll = 0;

    /* Fonction en JS */

window.addEventListener('scroll', function() {
    const currentScroll = window.pageYOffset;
    if (currentScroll > 50) {
        navbar.classListadd('shrink');
    }
    else {
        navbar.classList.remove('shrink');
    }
    
    lastScroll = currentScroll;
});

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