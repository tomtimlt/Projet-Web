<?php include_once __DIR__ . '/../Templates/header.php'; ?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?page=offers">Offres de stage</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                <?= isset($offer['id']) ? 'Modifier' : 'Créer' ?> une offre
            </li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header bg-light">
            <h2 class="mb-0">
                <i class="fas fa-<?= isset($offer['id']) ? 'edit' : 'plus' ?> me-2"></i>
                <?= isset($offer['id']) ? 'Modifier' : 'Créer' ?> une offre de stage
            </h2>
        </div>
        
        <div class="card-body">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Erreurs dans le formulaire</h5>
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form id="offerForm" action="index.php?page=offers&action=<?= isset($offer['id']) ? 'update' : 'store' ?>" method="POST" class="needs-validation" novalidate>
                <?php if (isset($offer['id'])): ?>
                    <input type="hidden" name="id" value="<?= $offer['id'] ?>">
                <?php endif; ?>
                
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h4 class="mb-3">Informations générales</h4>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre de l'offre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?= htmlspecialchars($offer['title']) ?>" required maxlength="150">
                            <div class="invalid-feedback">
                                Veuillez saisir un titre pour l'offre.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="company_id" class="form-label">Entreprise <span class="text-danger">*</span></label>
                            <select class="form-select" id="company_id" name="company_id" required>
                                <option value="">Sélectionner une entreprise</option>
                                <?php foreach ($companies as $company): ?>
                                    <option value="<?= $company['id'] ?>" <?= ($offer['company_id'] == $company['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($company['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                Veuillez sélectionner une entreprise.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="location" class="form-label">Lieu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="location" name="location" 
                                   value="<?= htmlspecialchars($offer['location']) ?>" required maxlength="100">
                            <div class="invalid-feedback">
                                Veuillez saisir le lieu du stage.
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Date de début <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="<?= htmlspecialchars($offer['start_date']) ?>" required>
                                <div class="invalid-feedback">
                                    Veuillez sélectionner une date de début.
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Date de fin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="<?= htmlspecialchars($offer['end_date']) ?>" required>
                                <div class="invalid-feedback">
                                    Veuillez sélectionner une date de fin.
                                </div>
                                <div id="date-error" class="text-danger d-none">
                                    La date de fin doit être postérieure à la date de début.
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="salary" class="form-label">Gratification mensuelle (€)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" class="form-control" id="salary" name="salary" 
                                       value="<?= htmlspecialchars($offer['salary']) ?>" placeholder="Ex: 600.00">
                                <span class="input-group-text">€</span>
                            </div>
                            <div class="form-text">Laisser vide si non rémunéré</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?= ($offer['status'] == 'active') ? 'selected' : '' ?>>Actif</option>
                                <option value="inactive" <?= ($offer['status'] == 'inactive') ? 'selected' : '' ?>>Inactif</option>
                                <option value="filled" <?= ($offer['status'] == 'filled') ? 'selected' : '' ?>>Pourvu</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h4 class="mb-3">Description</h4>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description détaillée <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="6" required><?= htmlspecialchars($offer['description']) ?></textarea>
                            <div class="invalid-feedback">
                                Veuillez saisir une description pour l'offre.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="skills_required" class="form-label">Compétences requises (description textuelle)</label>
                            <textarea class="form-control" id="skills_required" name="skills_required" rows="3"><?= htmlspecialchars($offer['skills_required']) ?></textarea>
                            <div class="form-text">Description libre des compétences requises (en complément des compétences sélectionnées ci-dessous)</div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h4 class="mb-3">Compétences</h4>
                        
                        <div class="mb-3">
                            <p class="form-text mb-2">Sélectionnez les compétences requises pour cette offre :</p>
                            
                            <div class="row">
                                <?php foreach ($skillsByCategory as $category => $skills): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <div class="card-header bg-light py-2">
                                                <h5 class="mb-0"><?= htmlspecialchars($category) ?></h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="skill-checkboxes">
                                                    <?php foreach ($skills as $skill): ?>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="skills[]" 
                                                                id="skill_<?= $skill['id'] ?>" value="<?= $skill['id'] ?>"
                                                                <?= in_array($skill['id'], (array)$offer['skills']) ? 'checked' : '' ?>>
                                                            <label class="form-check-label" for="skill_<?= $skill['id'] ?>">
                                                                <?= htmlspecialchars($skill['name']) ?>
                                                            </label>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php?page=offers" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        <?= isset($offer['id']) ? 'Mettre à jour' : 'Enregistrer' ?> l'offre
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation personnalisée du formulaire
    const form = document.getElementById('offerForm');
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const dateError = document.getElementById('date-error');
    
    // Fonction pour vérifier les dates
    function validateDates() {
        if (startDate.value && endDate.value) {
            if (new Date(endDate.value) <= new Date(startDate.value)) {
                dateError.classList.remove('d-none');
                endDate.setCustomValidity('La date de fin doit être postérieure à la date de début');
                return false;
            } else {
                dateError.classList.add('d-none');
                endDate.setCustomValidity('');
                return true;
            }
        }
        return true;
    }
    
    // Vérification des dates lors du changement
    startDate.addEventListener('change', validateDates);
    endDate.addEventListener('change', validateDates);
    
    // Validation au moment de la soumission
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity() || !validateDates()) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        form.classList.add('was-validated');
    });
});
</script>

<?php include_once __DIR__ . '/../Templates/footer.php'; ?>
