document.addEventListener("DOMContentLoaded", function () {
    const offersPerPage = 6; // Nombre d'offres par page
    const resultsContainer = document.querySelector(".results-container");
    const paginationContainer = document.querySelector(".pagination");
    const categoryFilter = document.getElementById("category");
    const locationFilter = document.getElementById("location");
    const durationFilter = document.getElementById("duration");
    const resetButton = document.getElementById("reset-filters"); // Bouton réinitialisation
    let currentPage = 1; // Page actuelle


    const offers = [
        { title: "Développeur Front-End", company: "TechInnovate", location: "Paris", duration: "6", category: "dev", url: "detail-offre1.html" },
        { title: "Développeur Back-End", company: "CodeSolutions", location: "Lyon", duration: "3", category: "dev", url: "detail-offre2.html" },
        { title: "Spécialiste Cybersécurité", company: "SecureNet", location: "Marseille", duration: "2", category: "securite", url: "detail-offre3.html" },
        { title: "Ingénieur Cloud & Réseau", company: "CloudMaster", location: "Nancy", duration: "1", category: "reseau", url: "detail-offre4.html" },
        { title: "Développeur Mobile iOS", company: "MobileTech", location: "Metz", duration: "3", category: "dev", url: "detail-offre5.html" },
        { title: "Analyste Sécurité Réseau", company: "CyberSafe", location: "Toulouse", duration: "4", category: "securite", url: "detail-offre6.html" },
        { title: "Data Scientist", company: "DataSolutions", location: "Paris", duration: "6", category: "data", url: "detail-offre7.html" },
        { title: "Architecte Réseau", company: "NetWorkExperts", location: "Bordeaux", duration: "5", category: "reseau", url: "detail-offre8.html" },
    ];

    let filteredOffers = [...offers]; // Toutes les offres au début

    function filterOffers() {
        const selectedCategory = categoryFilter.value;
        const selectedLocation = locationFilter.value;
        const selectedDuration = durationFilter.value;

        filteredOffers = offers.filter(offer => {
            return (
                (selectedCategory === "" || offer.category === selectedCategory) &&
                (selectedLocation === "" || offer.location.toLowerCase() === selectedLocation.toLowerCase()) &&
                (selectedDuration === "" || offer.duration === selectedDuration)
            );
        });

        currentPage = 1; // Retour à la première page après filtrage
        updatePagination();
    }

    function resetFilters() {
        categoryFilter.selectedIndex = 0;
        locationFilter.selectedIndex = 0;
        durationFilter.selectedIndex = 0;
        filterOffers();
    }

    function renderOffers() {
        resultsContainer.innerHTML = ""; // Supprime les anciennes offres
        const start = (currentPage - 1) * offersPerPage;
        const end = start + offersPerPage;
        const paginatedOffers = filteredOffers.slice(start, end);

        paginatedOffers.forEach(offer => {
            const offerCard = document.createElement("div");
            offerCard.classList.add("result-card");
            offerCard.onclick = () => window.location.href = offer.url;
            offerCard.innerHTML = `
                <div class="result-logo">
                    <img src="logo-ent.png" alt="Logo Entreprise">
                </div>
                <div class="result-details">
                    <h3>${offer.title}</h3>
                    <p><strong>Entreprise</strong>: ${offer.company}</p>
                    <p><strong>Localisation</strong>: ${offer.location}</p>
                    <p><strong>Durée</strong>: ${offer.duration} mois</p>
                </div>
            `;
            resultsContainer.appendChild(offerCard);
        });
    }

    function createPagination() {
        paginationContainer.innerHTML = ""; // Vider l'ancienne pagination
        const totalPages = Math.ceil(filteredOffers.length / offersPerPage);

        if (totalPages > 1) {
            const prevButton = document.createElement("a");
            prevButton.href = "#";
            prevButton.innerHTML = "&larr; Précédent";
            prevButton.classList.add("prev-btn");
            prevButton.addEventListener("click", function(e) {
                e.preventDefault();
                if (currentPage > 1) {
                    currentPage--;
                    updatePagination();
                }
            });
            paginationContainer.appendChild(prevButton);

            for (let i = 1; i <= totalPages; i++) {
                const pageLink = document.createElement("a");
                pageLink.href = "#";
                pageLink.textContent = i;
                pageLink.classList.toggle("active", i === currentPage);

                pageLink.addEventListener("click", function(e) {
                    e.preventDefault();
                    currentPage = i;
                    updatePagination();
                });

                paginationContainer.appendChild(pageLink);
            }

            const nextButton = document.createElement("a");
            nextButton.href = "#";
            nextButton.innerHTML = "Suivant &rarr;";
            nextButton.classList.add("next-btn");
            nextButton.addEventListener("click", function(e) {
                e.preventDefault();
                if (currentPage < totalPages) {
                    currentPage++;
                    updatePagination();
                }
            });
            paginationContainer.appendChild(nextButton);
        }
    }

    function updatePagination() {
        renderOffers();
        createPagination();
    }

    // Événements pour les filtres et réinitialisation
    categoryFilter.addEventListener("change", filterOffers);
    locationFilter.addEventListener("change", filterOffers);
    durationFilter.addEventListener("change", filterOffers);
    resetButton.addEventListener("click", resetFilters);

    updatePagination(); // Charge la pagination au démarrage
});
