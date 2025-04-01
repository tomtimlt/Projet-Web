/**
 * Script de validation du formulaire d'entreprise
 */
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('entrepriseForm');
    
    if (form) {
        form.addEventListener('submit', function(event) {
            // Ru00e9initialise les messages d'erreur
            const errorMessages = document.querySelectorAll('.error-message');
            errorMessages.forEach(function(element) {
                element.textContent = '';
            });
            
            // Vu00e9rifie si le formulaire est valide
            if (!validateForm()) {
                event.preventDefault();
            }
        });
        
        // Ajoute des u00e9couteurs d'u00e9vu00e9nements pour la validation en temps ru00e9el
        const inputs = form.querySelectorAll('input, textarea');
        inputs.forEach(function(input) {
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });
        
        // Validation spu00e9ciale pour le logo (taille et format)
        const logoInput = document.getElementById('logo');
        if (logoInput) {
            logoInput.addEventListener('change', function() {
                validateLogo(this);
            });
        }
    }
    
    /**
     * Valide tous les champs du formulaire
     * @return {boolean} True si tous les champs sont valides, false sinon
     */
    function validateForm() {
        let isValid = true;
        
        // Validation des champs requis
        const requiredFields = ['nom', 'secteur_activite', 'adresse', 'ville', 'code_postal', 'pays'];
        requiredFields.forEach(function(fieldId) {
            const field = document.getElementById(fieldId);
            if (field && !field.value.trim()) {
                showError(field, 'Ce champ est obligatoire');
                isValid = false;
            }
        });
        
        // Validation de l'email
        const emailField = document.getElementById('email');
        if (emailField && emailField.value.trim() && !isValidEmail(emailField.value.trim())) {
            showError(emailField, "Format d'email invalide");
            isValid = false;
        }
        
        // Validation de l'URL du site web
        const siteWebField = document.getElementById('site_web');
        if (siteWebField && siteWebField.value.trim() && !isValidUrl(siteWebField.value.trim())) {
            showError(siteWebField, "Format d'URL invalide");
            isValid = false;
        }
        
        // Validation du tu00e9lu00e9phone
        const telephoneField = document.getElementById('telephone');
        if (telephoneField && telephoneField.value.trim() && !isValidPhone(telephoneField.value.trim())) {
            showError(telephoneField, "Format de tu00e9lu00e9phone invalide");
            isValid = false;
        }
        
        // Validation du logo
        const logoField = document.getElementById('logo');
        if (logoField && logoField.files.length > 0) {
            if (!validateLogo(logoField)) {
                isValid = false;
            }
        }
        
        return isValid;
    }
    
    /**
     * Valide un champ spu00e9cifique
     * @param {HTMLElement} field Champ u00e0 valider
     * @return {boolean} True si le champ est valide, false sinon
     */
    function validateField(field) {
        // Ru00e9initialise le message d'erreur pour ce champ
        const errorElement = document.getElementById(field.id + '-error') || field.nextElementSibling;
        if (errorElement && errorElement.classList.contains('error-message')) {
            errorElement.textContent = '';
        }
        
        // Validation selon le type de champ
        switch (field.id) {
            case 'nom':
            case 'secteur_activite':
            case 'adresse':
            case 'ville':
            case 'code_postal':
            case 'pays':
                if (!field.value.trim()) {
                    showError(field, 'Ce champ est obligatoire');
                    return false;
                }
                break;
            case 'email':
                if (field.value.trim() && !isValidEmail(field.value.trim())) {
                    showError(field, "Format d'email invalide");
                    return false;
                }
                break;
            case 'site_web':
                if (field.value.trim() && !isValidUrl(field.value.trim())) {
                    showError(field, "Format d'URL invalide");
                    return false;
                }
                break;
            case 'telephone':
                if (field.value.trim() && !isValidPhone(field.value.trim())) {
                    showError(field, "Format de tu00e9lu00e9phone invalide");
                    return false;
                }
                break;
        }
        
        return true;
    }
    
    /**
     * Valide le fichier logo
     * @param {HTMLElement} field Champ de type file
     * @return {boolean} True si le fichier est valide, false sinon
     */
    function validateLogo(field) {
        if (field.files.length === 0) {
            return true; // Pas de fichier su00e9lectionnu00e9
        }
        
        const file = field.files[0];
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        const maxSize = 2 * 1024 * 1024; // 2 MB
        
        // Vu00e9rifie le type du fichier
        if (!allowedTypes.includes(file.type)) {
            showError(field, 'Format de fichier non autorisé. Utilisez JPG, PNG ou GIF.');
            return false;
        }
        
        // Vu00e9rifie la taille du fichier
        if (file.size > maxSize) {
            showError(field, 'Le fichier est trop volumineux. Taille maximale: 2 Mo.');
            return false;
        }
        
        return true;
    }
    
    /**
     * Affiche un message d'erreur pour un champ
     * @param {HTMLElement} field Champ concernu00e9
     * @param {string} message Message d'erreur u00e0 afficher
     */
    function showError(field, message) {
        const errorElement = document.getElementById(field.id + '-error') || field.nextElementSibling;
        if (errorElement && errorElement.classList.contains('error-message')) {
            errorElement.textContent = message;
        }
        field.classList.add('error');
    }
    
    /**
     * Vu00e9rifie si une chaîne est un email valide
     * @param {string} email Email u00e0 vu00e9rifier
     * @return {boolean} True si l'email est valide, false sinon
     */
    function isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
    
    /**
     * Vu00e9rifie si une chaîne est une URL valide
     * @param {string} url URL u00e0 vu00e9rifier
     * @return {boolean} True si l'URL est valide, false sinon
     */
    function isValidUrl(url) {
        try {
            new URL(url);
            return true;
        } catch (e) {
            return false;
        }
    }
    
    /**
     * Vu00e9rifie si une chaîne est un numu00e9ro de tu00e9lu00e9phone valide
     * @param {string} phone Numu00e9ro u00e0 vu00e9rifier
     * @return {boolean} True si le numu00e9ro est valide, false sinon
     */
    function isValidPhone(phone) {
        // Format international flexible avec ou sans le +
        const regex = /^(?:\+?\d{1,3}[- ]?)?(?:\(?\d{1,6}\)?[- ]?)?\d{1,14}(?:[- ]?\d{1,5})?$/;
        return regex.test(phone);
    }
});
