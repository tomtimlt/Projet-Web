<?php
$pageTitle = isset($student) ? "Modifier l'étudiant" : "Ajouter un étudiant";
require_once __DIR__ . '/../Templates/header.php';
$auth = \Models\Auth::getInstance();

// Définir isEditMode s'il n'est pas déjà défini
if (!isset($isEditMode)) {
    $isEditMode = isset($student) && is_array($student) && !empty($student);
}
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">
                        <?= $isEditMode ? "Modifier l'étudiant" : "Nouvel étudiant" ?>
                    </h2>
                </div>
                <div class="card-body">
                    <!-- Affichage des messages flash -->
                    <?php require_once 'Views/Templates/flash.php'; ?>

                    <form action="index.php?controller=student&action=<?= $isEditMode ? 'update' : 'store' ?>" 
                          method="POST" class="needs-validation" novalidate>
                        
                        <?php if ($isEditMode) : ?>
                            <input type="hidden" name="id" value="<?= htmlspecialchars($student['id']) ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="lastname" class="form-label">Nom *</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" 
                                   value="<?= $isEditMode ? htmlspecialchars($student['lastname']) : '' ?>" 
                                   required pattern="[A-Za-zÀ-ÖØ-öø-ÿ\-' ]+" minlength="2" maxlength="50">
                            <div class="invalid-feedback">
                                Le nom est requis et doit contenir entre 2 et 50 caractères alphabétiques.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="firstname" class="form-label">Prénom *</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" 
                                   value="<?= $isEditMode ? htmlspecialchars($student['firstname']) : '' ?>" 
                                   required pattern="[A-Za-zÀ-ÖØ-öø-ÿ\-' ]+" minlength="2" maxlength="50">
                            <div class="invalid-feedback">
                                Le prénom est requis et doit contenir entre 2 et 50 caractères alphabétiques.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= $isEditMode ? htmlspecialchars($student['email']) : '' ?>" 
                                   required maxlength="100">
                            <div class="invalid-feedback">
                                Veuillez fournir une adresse email valide.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <?= $isEditMode ? "Mot de passe (laisser vide pour ne pas modifier)" : "Mot de passe *" ?>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" 
                                       <?= $isEditMode ? '' : 'required' ?> minlength="8" maxlength="255">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                Le mot de passe doit contenir au moins 8 caractères.
                            </div>
                            <div class="invalid-feedback">
                                Le mot de passe doit contenir au moins 8 caractères.
                            </div>
                        </div>
                        
                        <?php if (!$isEditMode) : ?>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Confirmer le mot de passe *</label>
                                <input type="password" class="form-control" id="confirmPassword" 
                                       required minlength="8" maxlength="255">
                                <div class="invalid-feedback">
                                    Les mots de passe ne correspondent pas.
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="index.php?controller=student&action=<?= $isEditMode ? "show&id={$student['id']}" : 'index' ?>" 
                               class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?= $isEditMode ? "Mettre à jour" : "Enregistrer" ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Validation côté client avec feedback
    (function() {
        'use strict';
        
        // Fetch all forms we want to apply custom validation
        var forms = document.querySelectorAll('.needs-validation');
        
        // Validation pour la correspondance des mots de passe
        var password = document.getElementById('password');
        var confirmPassword = document.getElementById('confirmPassword');
        
        function validatePasswordMatch() {
            if (confirmPassword && password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Les mots de passe ne correspondent pas.');
            } else if (confirmPassword) {
                confirmPassword.setCustomValidity('');
            }
        }
        
        if (password) {
            password.addEventListener('input', validatePasswordMatch);
        }
        
        if (confirmPassword) {
            confirmPassword.addEventListener('input', validatePasswordMatch);
        }
        
        // Afficher/masquer le mot de passe
        var togglePassword = document.getElementById('togglePassword');
        togglePassword.addEventListener('click', function() {
            var type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
        
        // Loop over forms and prevent submission
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>

<?php require_once __DIR__ . '/../Templates/footer.php'; ?>
