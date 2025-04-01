<?php include 'views/header.php'; ?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-8">
            <h1>Ajouter un nouveau rôle</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="index.php?controller=role&action=index" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="index.php?controller=role&action=store" method="post" id="roleForm">
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom du rôle <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nom" name="nom" required>
                    <div class="invalid-feedback">Veuillez entrer un nom pour ce rôle.</div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Permissions <span class="text-danger">*</span></label>
                    
                    <?php if (empty($permissions)): ?>
                        <div class="alert alert-warning">
                            Aucune permission n'est définie dans le système.
                        </div>
                    <?php else: ?>
                        <div class="permission-grid">
                            <?php foreach ($permissions as $permission): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" 
                                           value="<?php echo $permission->id; ?>" 
                                           id="perm_<?php echo $permission->id; ?>">
                                    <label class="form-check-label" for="perm_<?php echo $permission->id; ?>">
                                        <strong><?php echo htmlspecialchars($permission->nom); ?></strong>
                                        <?php if (!empty($permission->description)): ?>
                                            <p class="text-muted small mb-0"><?php echo htmlspecialchars($permission->description); ?></p>
                                        <?php endif; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="selectAll">Tout sélectionner</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAll">Tout désélectionner</button>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-outline-secondary">Réinitialiser</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('roleForm');
        const selectAllBtn = document.getElementById('selectAll');
        const deselectAllBtn = document.getElementById('deselectAll');
        const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
        
        // Validation du formulaire
        form.addEventListener('submit', function(event) {
            let valid = true;
            
            // Valider le nom
            const nomInput = document.getElementById('nom');
            if (!nomInput.value.trim()) {
                nomInput.classList.add('is-invalid');
                valid = false;
            } else {
                nomInput.classList.remove('is-invalid');
            }
            
            // Vérifier qu'au moins une permission est sélectionnée
            const permissionsChecked = document.querySelectorAll('input[name="permissions[]"]:checked');
            if (permissionsChecked.length === 0 && checkboxes.length > 0) {
                alert('Veuillez sélectionner au moins une permission pour ce rôle.');
                valid = false;
            }
            
            if (!valid) {
                event.preventDefault();
            }
        });
        
        // Boutons de sélection/désélection
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
            });
        }
        
        if (deselectAllBtn) {
            deselectAllBtn.addEventListener('click', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
            });
        }
    });
</script>

<style>
    .permission-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 10px;
        margin-bottom: 10px;
    }
    
    @media (max-width: 768px) {
        .permission-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php include 'views/footer.php'; ?>
