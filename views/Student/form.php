<?php include_once __DIR__ . '/../Templates/header.php'; ?>

<?php
// Définir si nous sommes en mode édition ou création
$isEditMode = isset($student['id']);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-user-graduate me-2"></i><?= $pageTitle ?></h1>
        <a href="index.php?page=students" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Retour à la liste
        </a>
    </div>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Des erreurs sont survenues</h5>
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <?php if (isset($student['id'])): ?>
                    <i class="fas fa-edit me-2"></i>Modification d'un compte étudiant
                <?php else: ?>
                    <i class="fas fa-plus-circle me-2"></i>Création d'un compte étudiant
                <?php endif; ?>
            </h5>
        </div>
        <div class="card-body">
            <form method="post" action="index.php?page=students&action=<?= isset($student['id']) ? 'update&id=' . $student['id'] : 'store' ?>">
                <?php if (isset($student['id'])): ?>
                    <input type="hidden" name="id" value="<?= $student['id'] ?>">
                <?php endif; ?>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="lastname" class="form-label">Nom</label>
                        <input type="text" class="form-control <?= isset($errors['lastname']) ? 'is-invalid' : '' ?>" 
                               id="lastname" name="lastname" 
                               value="<?= htmlspecialchars($student['lastname'] ?? '') ?>" required>
                        <?php if (isset($errors['lastname'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['lastname']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label for="firstname" class="form-label">Prénom</label>
                        <input type="text" class="form-control <?= isset($errors['firstname']) ? 'is-invalid' : '' ?>" 
                               id="firstname" name="firstname" 
                               value="<?= htmlspecialchars($student['firstname'] ?? '') ?>" required>
                        <?php if (isset($errors['firstname'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['firstname']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse email</label>
                    <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                           id="email" name="email" 
                           value="<?= htmlspecialchars($student['email'] ?? '') ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">
                            Mot de passe
                            <?php if (isset($student['id'])): ?>
                                <small class="text-muted">(Laisser vide pour ne pas modifier)</small>
                            <?php endif; ?>
                        </label>
                        <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                               id="password" name="password" 
                               <?= isset($student['id']) ? '' : 'required' ?>>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirm" class="form-label">
                            Confirmer le mot de passe
                            <?php if (isset($student['id'])): ?>
                                <small class="text-muted">(Laisser vide pour ne pas modifier)</small>
                            <?php endif; ?>
                        </label>
                        <input type="password" class="form-control <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>" 
                               id="password_confirm" name="password_confirm" 
                               <?= isset($student['id']) ? '' : 'required' ?>>
                        <?php if (isset($errors['password_confirm'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['password_confirm']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                               <?= (isset($student['is_active']) && $student['is_active'] == 1) || !isset($student['id']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Compte actif</label>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        <?= isset($student['id']) ? 'Enregistrer les modifications' : 'Créer l\'étudiant' ?>
                    </button>
                    <a href="index.php?page=students" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../Templates/footer.php'; ?>
