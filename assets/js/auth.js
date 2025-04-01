/**
 * Script de gestion des interactions sur la page d'authentification
 */
document.addEventListener('DOMContentLoaded', function() {
    // Récupération du formulaire de connexion
    const loginForm = document.querySelector('form');
    
    if (loginForm) {
        // Validation du formulaire côté client
        loginForm.addEventListener('submit', function(event) {
            let isValid = true;
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            
            // Validation de l'email
            if (!emailInput.value.trim()) {
                isValid = false;
                showError(emailInput, "L'email est requis");
            } else if (!isValidEmail(emailInput.value.trim())) {
                isValid = false;
                showError(emailInput, "Format d'email invalide");
            } else {
                removeError(emailInput);
            }
            
            // Validation du mot de passe
            if (!passwordInput.value) {
                isValid = false;
                showError(passwordInput, "Le mot de passe est requis");
            } else {
                removeError(passwordInput);
            }
            
            // Empêche la soumission si le formulaire est invalide
            if (!isValid) {
                event.preventDefault();
            }
        });
    }
    
    /**
     * Affiche un message d'erreur pour un champ spécifique
     * @param {HTMLElement} input Élément de saisie
     * @param {string} message Message d'erreur à afficher
     */
    function showError(input, message) {
        // Supprime l'ancien message d'erreur s'il existe
        removeError(input);
        
        // Crée un nouvel élément pour le message d'erreur
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        errorDiv.style.color = '#dc3545';
        errorDiv.style.fontSize = '12px';
        errorDiv.style.marginTop = '5px';
        
        // Ajoute une bordure rouge à l'input
        input.style.borderColor = '#dc3545';
        
        // Insère le message après l'input
        input.parentNode.insertBefore(errorDiv, input.nextSibling);
    }
    
    /**
     * Supprime le message d'erreur associé à un champ
     * @param {HTMLElement} input Élément de saisie
     */
    function removeError(input) {
        // Réinitialise la bordure
        input.style.borderColor = '';
        
        // Recherche et supprime le message d'erreur s'il existe
        const errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.className === 'error-message') {
            errorDiv.parentNode.removeChild(errorDiv);
        }
    }
    
    /**
     * Vérifie si une chaîne est un email valide
     * @param {string} email Email à vérifier
     * @return {boolean} True si l'email est valide, false sinon
     */
    function isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
});
