<?php require_once 'views/Templates/header.php'; ?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Changer mon mot de passe</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($_SESSION['error']) ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?= htmlspecialchars($_SESSION['success']) ?>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    
                    <form method="POST" action="index.php?page=password&action=change<?= isset($id) && $id != $_SESSION['user_id'] ? '&id=' . $id : '' ?>">
                        <?php if (!isset($id) || $id == $_SESSION['user_id']): ?>
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mot de passe actuel</label>
                            <input type="password" class="form-control <?= isset($errors['current_password']) ? 'is-invalid' : '' ?>" 
                                   id="current_password" name="current_password">
                            <?php if (isset($errors['current_password'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($errors['current_password']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nouveau mot de passe</label>
                            <input type="password" class="form-control <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>" 
                                   id="new_password" name="new_password">
                            <?php if (isset($errors['new_password'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($errors['new_password']) ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">Le mot de passe doit contenir au moins 8 caractères.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmer le nouveau mot de passe</label>
                            <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" 
                                   id="confirm_password" name="confirm_password">
                            <?php if (isset($errors['confirm_password'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($errors['confirm_password']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="index.php?page=profile" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/Templates/footer.php'; ?>

<script>
// Validation en temps réel des mots de passe
document.addEventListener('DOMContentLoaded', function() {
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const form = document.querySelector('form');
    
    function validatePasswords() {
        if (confirmPasswordInput.value === '') {
            // Champ vide, ne pas afficher d'erreur
            confirmPasswordInput.classList.remove('is-invalid');
            confirmPasswordInput.classList.remove('is-valid');
            return;
        }
        
        if (newPasswordInput.value === confirmPasswordInput.value) {
            confirmPasswordInput.classList.remove('is-invalid');
            confirmPasswordInput.classList.add('is-valid');
        } else {
            confirmPasswordInput.classList.remove('is-valid');
            confirmPasswordInput.classList.add('is-invalid');
            
            // Créer un message d'erreur s'il n'existe pas
            let feedback = confirmPasswordInput.nextElementSibling;
            if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                feedback = document.createElement('div');
                feedback.classList.add('invalid-feedback');
                feedback.textContent = "Les mots de passe ne correspondent pas.";
                confirmPasswordInput.parentNode.appendChild(feedback);
            }
        }
    }
    
    // Validation à la frappe
    confirmPasswordInput.addEventListener('input', validatePasswords);
    newPasswordInput.addEventListener('input', function() {
        if (confirmPasswordInput.value !== '') {
            validatePasswords();
        }
    });
    
    // Validation au submit
    form.addEventListener('submit', function(event) {
        if (newPasswordInput.value.length < 8) {
            newPasswordInput.classList.add('is-invalid');
            event.preventDefault();
        }
        
        if (newPasswordInput.value !== confirmPasswordInput.value) {
            confirmPasswordInput.classList.add('is-invalid');
            event.preventDefault();
        }
    });
});
</script>
