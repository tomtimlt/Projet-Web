<?php include_once __DIR__ . '/../Templates/header.php'; ?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?page=users">Gestion des utilisateurs</a></li>
            <li class="breadcrumb-item active" aria-current="page">Créer un compte utilisateur</li>
        </ol>
    </nav>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">
                <i class="fas fa-plus me-2"></i>
                Créer un compte utilisateur
            </h2>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= $_SESSION['error'] ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="index.php?page=user&action=create" method="POST" class="needs-validation" novalidate>
                <div class="row g-3">
                    <!-- Prénom -->
                    <div class="col-md-6">
                        <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= isset($errors['prenom']) ? 'is-invalid' : '' ?>" 
                               id="prenom" name="prenom" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
                        <?php if (isset($errors['prenom'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['prenom'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Nom -->
                    <div class="col-md-6">
                        <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= isset($errors['nom']) ? 'is-invalid' : '' ?>" 
                               id="nom" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                        <?php if (isset($errors['nom'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['nom'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Email -->
                    <div class="col-md-6">
                        <label for="email" class="form-label">Adresse email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                               id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['email'] ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-text">Cet email servira d'identifiant de connexion.</div>
                    </div>
                    
                    <!-- Téléphone -->
                    <div class="col-md-6">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control <?= isset($errors['telephone']) ? 'is-invalid' : '' ?>" 
                               id="telephone" name="telephone" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
                        <?php if (isset($errors['telephone'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['telephone'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Rôle -->
                    <div class="col-md-6">
                        <label for="role" class="form-label">Rôle <span class="text-danger">*</span></label>
                        <select class="form-select <?= isset($errors['role']) ? 'is-invalid' : '' ?>" 
                                id="role" name="role" required>
                            <option value="" selected disabled>Sélectionner un rôle</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>" <?= (isset($_POST['role']) && $_POST['role'] == $role['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($role['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['role'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['role'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Centre -->
                    <div class="col-md-6">
                        <label for="centre" class="form-label">Centre</label>
                        <input type="text" class="form-control <?= isset($errors['centre']) ? 'is-invalid' : '' ?>" 
                               id="centre" name="centre" value="<?= htmlspecialchars($_POST['centre'] ?? '') ?>">
                        <?php if (isset($errors['centre'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['centre'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Promotion -->
                    <div class="col-md-6">
                        <label for="promotion" class="form-label">Promotion</label>
                        <input type="text" class="form-control <?= isset($errors['promotion']) ? 'is-invalid' : '' ?>" 
                               id="promotion" name="promotion" value="<?= htmlspecialchars($_POST['promotion'] ?? '') ?>">
                        <?php if (isset($errors['promotion'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['promotion'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-12">
                        <hr>
                        <h5 class="mb-3">Informations de connexion</h5>
                    </div>
                    
                    <!-- Mot de passe -->
                    <div class="col-md-6">
                        <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                        <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                               id="password" name="password" required>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['password'] ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-text">Le mot de passe doit contenir au moins 8 caractères.</div>
                    </div>
                    
                    <!-- Confirmation du mot de passe -->
                    <div class="col-md-6">
                        <label for="confirm_password" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                        <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" 
                               id="confirm_password" name="confirm_password" required>
                        <?php if (isset($errors['confirm_password'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['confirm_password'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Boutons d'action -->
                    <div class="col-12 mt-4">
                        <div class="d-flex justify-content-end">
                            <a href="index.php?page=users" class="btn btn-secondary me-2">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Créer l'utilisateur
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
        const passwordConfirm = document.getElementById('confirm_password');
        
        if (password.value !== '' && password.value.length < 8) {
            password.setCustomValidity('Le mot de passe doit contenir au moins 8 caractères.');
            event.preventDefault();
        } else {
            password.setCustomValidity('');
        }
        
        if (password.value !== passwordConfirm.value) {
            passwordConfirm.setCustomValidity('Les mots de passe ne correspondent pas.');
            event.preventDefault();
        } else {
            passwordConfirm.setCustomValidity('');
        }
    }, false);
});
</script>

<?php include_once __DIR__ . '/../Templates/footer.php'; ?>
