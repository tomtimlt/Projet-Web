<?php include_once __DIR__ . '/../Templates/header.php'; ?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?page=pilots">Gestion des pilotes</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                <?= isset($pilot['id']) ? 'Modifier' : 'Créer' ?> un compte pilote
            </li>
        </ol>
    </nav>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">
                <i class="fas fa-<?= isset($pilot['id']) ? 'edit' : 'plus' ?> me-2"></i>
                <?= isset($pilot['id']) ? 'Modifier' : 'Créer' ?> un compte pilote
            </h2>
        </div>
        <div class="card-body">
            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= $errors['general'] ?>
                </div>
            <?php endif; ?>

            <form action="index.php?page=pilots&action=<?= isset($pilot['id']) ? 'update' : 'store' ?>" method="POST" class="needs-validation" novalidate>
                <?php if (isset($pilot['id'])): ?>
                    <input type="hidden" name="id" value="<?= $pilot['id'] ?>">
                <?php endif; ?>
                
                <div class="row g-3">
                    <!-- Prénom -->
                    <div class="col-md-6">
                        <label for="firstname" class="form-label">Prénom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= isset($errors['firstname']) ? 'is-invalid' : '' ?>" 
                               id="firstname" name="firstname" value="<?= htmlspecialchars($pilot['firstname'] ?? '') ?>" required>
                        <?php if (isset($errors['firstname'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['firstname'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Nom -->
                    <div class="col-md-6">
                        <label for="lastname" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= isset($errors['lastname']) ? 'is-invalid' : '' ?>" 
                               id="lastname" name="lastname" value="<?= htmlspecialchars($pilot['lastname'] ?? '') ?>" required>
                        <?php if (isset($errors['lastname'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['lastname'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Email -->
                    <div class="col-md-12">
                        <label for="email" class="form-label">Adresse email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                               id="email" name="email" value="<?= htmlspecialchars($pilot['email'] ?? '') ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['email'] ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-text">Cet email servira d'identifiant de connexion.</div>
                    </div>
                    
                    <!-- Mot de passe -->
                    <div class="col-md-6">
                        <label for="password" class="form-label">
                            Mot de passe <?= isset($pilot['id']) ? '' : '<span class="text-danger">*</span>' ?>
                        </label>
                        <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                               id="password" name="password" <?= isset($pilot['id']) ? '' : 'required' ?>>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['password'] ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($pilot['id'])): ?>
                            <div class="form-text">Laissez vide pour conserver le mot de passe actuel.</div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Confirmation du mot de passe -->
                    <div class="col-md-6">
                        <label for="password_confirm" class="form-label">
                            Confirmer le mot de passe <?= isset($pilot['id']) ? '' : '<span class="text-danger">*</span>' ?>
                        </label>
                        <input type="password" class="form-control <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>" 
                               id="password_confirm" name="password_confirm" <?= isset($pilot['id']) ? '' : 'required' ?>>
                        <?php if (isset($errors['password_confirm'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['password_confirm'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Boutons d'action -->
                    <div class="col-12 mt-4">
                        <div class="d-flex justify-content-end">
                            <a href="index.php?page=pilots" class="btn btn-secondary me-2">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i><?= isset($pilot['id']) ? 'Enregistrer' : 'Créer' ?>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Validation du formulaire côté client
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer le formulaire
    const form = document.querySelector('.needs-validation');
    
    // Gestionnaire pour bloquer l'envoi si non valide
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        // Ajouter la classe was-validated pour afficher les styles de validation
        form.classList.add('was-validated');
        
        // Validation personnalisée pour les mots de passe
        const password = document.getElementById('password');
        const passwordConfirm = document.getElementById('password_confirm');
        
        if (password.value !== '' && password.value.length < 8) {
            password.setCustomValidity('Le mot de passe doit contenir au moins 8 caractères.');
        } else {
            password.setCustomValidity('');
        }
        
        if (password.value !== passwordConfirm.value) {
            passwordConfirm.setCustomValidity('Les mots de passe ne correspondent pas.');
        } else {
            passwordConfirm.setCustomValidity('');
        }
    }, false);
});
</script>

<?php include_once __DIR__ . '/../Templates/footer.php'; ?>
