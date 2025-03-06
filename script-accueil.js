document.addEventListener("DOMContentLoaded", function () {
    const offersContainer = document.getElementById("offers-container");

    if (offersContainer) { // Vérifie si on est bien sur la page d'accueil
        const offers = [
            { title: "Développeur Front-End", company: "TechInnovate", location: "Paris", duration: "6", category: "dev", url: "detail-offre1.html" },
            { title: "Développeur Back-End", company: "CodeSolutions", location: "Lyon", duration: "3", category: "dev", url: "detail-offre2.html" },
            { title: "Spécialiste Cybersécurité", company: "SecureNet", location: "Marseille", duration: "2", category: "securite", url: "detail-offre3.html" },
            { title: "Ingénieur Cloud & Réseau", company: "CloudMaster", location: "Nancy", duration: "1", category: "reseau", url: "detail-offre4.html" },
            { title: "Développeur Mobile iOS", company: "MobileTech", location: "Metz", duration: "3", category: "dev", url: "detail-offre5.html" },
            { title: "Analyste Sécurité Réseau", company: "CyberSafe", location: "Toulouse", duration: "4", category: "securite", url: "detail-offre6.html" },
        ];

        offers.forEach(offer => {
            const offerCard = document.createElement("div");
            offerCard.classList.add("offer-card");
            offerCard.onclick = () => window.location.href = offer.url;
            offerCard.innerHTML = `
                <div class="company-logo">
                    <img src="/api/placeholder/60/60" alt="Logo Entreprise">
                </div>
                <div class="offer-details">
                    <h3>${offer.title}</h3>
                    <p><strong>Entreprise :</strong> ${offer.company}</p>
                    <p><strong>Localisation :</strong> ${offer.location}</p>
                    <p><strong>Durée :</strong> ${offer.duration} mois</p>
                </div>
            `;
            offersContainer.appendChild(offerCard);
        });
    }
});
