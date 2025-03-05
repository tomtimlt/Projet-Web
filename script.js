document.addEventListener("DOMContentLoaded", function() {
    
    // Variables en JS
    const cookieNotification = document.getElementById("cookie-notification");
    const acceptButton = document.getElementById("accept-cookies");
    const declineButton = document.getElementById("decline-cookies");
    const cookiePolicy = document.getElementById("cookie-policy");
    const validateButton = document.getElementById("validation");
    const rechercheBtn = document.querySelector(".recherche-btn");
    const searchContainer = document.querySelector(".search-container");
    
    // Gestion des cookies
    if (localStorage.getItem("cookiesAccepted") === "true") {
        cookieNotification.style.display = "flex";
    } else {
        cookieNotification.style.display = "flex";
    }

    acceptButton.addEventListener("click", function() {
        localStorage.setItem("cookiesAccepted", "true");
        cookieNotification.style.display = "none";
    });

    // Correction: searchButton n'existait pas, utilisation de rechercheBtn à la place
    rechercheBtn.addEventListener("click", function() {
        searchContainer.style.display = "flex";
    });

    declineButton.addEventListener("click", function() {
        cookieNotification.style.display = "none";
        cookiePolicy.style.display = "flex";
    });

    validateButton.addEventListener("click", function() {
        cookiePolicy.style.display = "none";
    });

    // Navigation
    const connexionBtn = document.querySelector(".connexion-btn");
    if (connexionBtn) {
        connexionBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'connexion.html';
        });
    }

    const indexBtn = document.querySelector(".index-btn");
    if (indexBtn) {
        indexBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'index.html';
        });
    }

    const rechercheButton = document.querySelector(".recherche-btn");
    if (rechercheButton) {
        rechercheButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'recherche.html';
        });
    }

    // Gestion du formulaire de connexion (si présent sur la page)
    const loginForm = document.querySelector(".login-form");
    if (loginForm) {
        loginForm.addEventListener("submit", function(event) {
            event.preventDefault();
            const username = document.getElementById("username").value;
            const password = document.getElementById("password").value;
            if (username && password) {
                alert("Connexion réussie !");
            } else {
                alert("Veuillez remplir tous les champs.");
            }
        });
    }

    // Gestion de la barre de recherche
    const searchBtn = document.querySelector(".search-btn");
    const searchInput = document.querySelector(".search-bar");

    if (searchBtn && searchInput) {
        searchBtn.addEventListener("click", function() {
            const searchValue = searchInput.value.trim();
            if (searchValue !== "") {
                alert("Recherche en cours pour : " + searchValue);
                // filtre
            } else {
                alert("Veuillez entrer un mot-clé.");
            }
        });
    }
});