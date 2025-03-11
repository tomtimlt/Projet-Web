<?php
// Déterminer s'il s'agit d'une création ou d'une modification
$isEditing = isset($company['id']);
$formTitle = $isEditing ? 'Modifier l\'entreprise' : 'Ajouter une entreprise';
$submitText = $isEditing ? 'Enregistrer les modifications' : 'Créer l\'entreprise';
$formAction = $isEditing ? 'update' : 'store';

// Définir le titre de la page
$pageTitle = $formTitle;

// S'assurer que toutes les clés sont définies pour éviter les erreurs
$company = array_merge([
    'name' => '',
    'sector' => '',
    'size' => '',
    'address' => '',
    'postal_code' => '',
    'city' => '',
    'country' => '',
    'phone' => '',
    'email' => '',
    'website' => '',
    'description' => ''
], $company ?? []);

// Inclusion du header
require_once __DIR__ . '/../Templates/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h2 class="mb-0"><?= $formTitle ?></h2>
                <a href="index.php?page=companies" class="btn btn-light">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger">
                    <?= $errors['general'] ?>
                </div>
                <?php endif; ?>
                
                <form action="index.php?page=companies&action=<?= $formAction ?>" method="post" class="needs-validation" novalidate>
                    <?php if ($isEditing): ?>
                    <input type="hidden" name="id" value="<?= $company['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="row g-3">
                        <!-- Nom de l'entreprise -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nom de l'entreprise <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                   id="name" name="name" value="<?= htmlspecialchars($company['name']) ?>" required>
                            <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['name'] ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Secteur d'activité -->
                        <div class="col-md-6">
                            <label for="sector" class="form-label">Secteur d'activité</label>
                            <input type="text" class="form-control <?= isset($errors['sector']) ? 'is-invalid' : '' ?>" 
                                   id="sector" name="sector" value="<?= htmlspecialchars($company['sector']) ?>">
                            <?php if (isset($errors['sector'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['sector'] ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Taille de l'entreprise -->
                        <div class="col-md-6">
                            <label for="size" class="form-label">Taille de l'entreprise</label>
                            <select class="form-select <?= isset($errors['size']) ? 'is-invalid' : '' ?>" id="size" name="size">
                                <option value="" <?= empty($company['size']) ? 'selected' : '' ?>>Sélectionner une taille</option>
                                <option value="TPE" <?= $company['size'] === 'TPE' ? 'selected' : '' ?>>TPE (Très Petite Entreprise, < 10 salariés)</option>
                                <option value="PME" <?= $company['size'] === 'PME' ? 'selected' : '' ?>>PME (Petite et Moyenne Entreprise, 10-250 salariés)</option>
                                <option value="ETI" <?= $company['size'] === 'ETI' ? 'selected' : '' ?>>ETI (Entreprise de Taille Intermédiaire, 250-5000 salariés)</option>
                                <option value="GE" <?= $company['size'] === 'GE' ? 'selected' : '' ?>>GE (Grande Entreprise, > 5000 salariés)</option>
                            </select>
                            <?php if (isset($errors['size'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['size'] ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Adresse -->
                        <div class="col-md-6">
                            <label for="address" class="form-label">Adresse <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>" 
                                   id="address" name="address" value="<?= htmlspecialchars($company['address']) ?>" required>
                            <?php if (isset($errors['address'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['address'] ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Code postal -->
                        <div class="col-md-4">
                            <label for="postal_code" class="form-label">Code postal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['postal_code']) ? 'is-invalid' : '' ?>" 
                                   id="postal_code" name="postal_code" value="<?= htmlspecialchars($company['postal_code']) ?>" required>
                            <?php if (isset($errors['postal_code'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['postal_code'] ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Ville -->
                        <div class="col-md-4">
                            <label for="city" class="form-label">Ville <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['city']) ? 'is-invalid' : '' ?>" 
                                   id="city" name="city" value="<?= htmlspecialchars($company['city']) ?>" required>
                            <?php if (isset($errors['city'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['city'] ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Pays -->
                        <div class="col-md-4">
                            <label for="country" class="form-label">Pays <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['country']) ? 'is-invalid' : '' ?>" 
                                   id="country" name="country" value="<?= htmlspecialchars($company['country']) ?>" required>
                            <?php if (isset($errors['country'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['country'] ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Téléphone -->
                        <div class="col-md-4">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="text" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                                   id="phone" name="phone" value="<?= htmlspecialchars($company['phone']) ?>">
                            <?php if (isset($errors['phone'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['phone'] ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Email -->
                        <div class="col-md-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                   id="email" name="email" value="<?= htmlspecialchars($company['email']) ?>">
                            <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['email'] ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Site web -->
                        <div class="col-md-4">
                            <label for="website" class="form-label">Site web</label>
                            <input type="url" class="form-control <?= isset($errors['website']) ? 'is-invalid' : '' ?>" 
                                   id="website" name="website" value="<?= htmlspecialchars($company['website']) ?>" 
                                   placeholder="https://www.example.com">
                            <?php if (isset($errors['website'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['website'] ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Description -->
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                                      id="description" name="description" rows="5"><?= htmlspecialchars($company['description']) ?></textarea>
                            <?php if (isset($errors['description'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['description'] ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Les champs marqués d'une étoile (<span class="text-danger">*</span>) sont obligatoires.
                            </div>
                        </div>
                        
                        <div class="col-12 d-flex justify-content-end">
                            <a href="index.php?page=companies" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?= $submitText ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Validation Bootstrap des formulaires
    (function() {
        'use strict';
        
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation');
        
        // Loop over them and prevent submission
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

<?php
// Inclusion du footer
require_once __DIR__ . '/../Templates/footer.php';
?>
