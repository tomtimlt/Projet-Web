<?php
// Titre de la page
$pageTitle = "Liste des entreprises";

// Inclusion du header
require_once __DIR__ . '/../Templates/header.php';
?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Liste des entreprises</h2>
                    <?php if ($this->auth->hasPermission('SFx3')): ?>
                    <a href="index.php?page=companies&action=create" class="btn btn-light">
                        <i class="fas fa-plus"></i> Ajouter une entreprise
                    </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <!-- Filtres de recherche -->
                    <form method="get" action="index.php" class="mb-4">
                        <input type="hidden" name="page" value="companies">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="name" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($filters['name']) ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="city" class="form-label">Ville</label>
                                <input type="text" class="form-control" id="city" name="city" value="<?= htmlspecialchars($filters['city']) ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="sector" class="form-label">Secteur</label>
                                <select class="form-select" id="sector" name="sector">
                                    <option value="">Tous les secteurs</option>
                                    <?php foreach ($sectors as $sector): ?>
                                        <option value="<?= htmlspecialchars($sector) ?>" <?= $filters['sector'] === $sector ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($sector) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="size" class="form-label">Taille</label>
                                <select class="form-select" id="size" name="size">
                                    <option value="">Toutes les tailles</option>
                                    <option value="TPE" <?= $filters['size'] === 'TPE' ? 'selected' : '' ?>>TPE</option>
                                    <option value="PME" <?= $filters['size'] === 'PME' ? 'selected' : '' ?>>PME</option>
                                    <option value="ETI" <?= $filters['size'] === 'ETI' ? 'selected' : '' ?>>ETI</option>
                                    <option value="GE" <?= $filters['size'] === 'GE' ? 'selected' : '' ?>>GE</option>
                                </select>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> Rechercher
                                </button>
                                <a href="index.php?page=companies" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> Réinitialiser
                                </a>
                            </div>
                        </div>
                    </form>
                    
                    <?php if ($this->auth->hasPermission('SFx7')): ?>
                    <div class="mb-3 text-end">
                        <a href="index.php?page=companies&action=stats" class="btn btn-info">
                            <i class="fas fa-chart-bar"></i> Voir les statistiques
                        </a>
                    </div>
                    <?php endif; ?>

                    <!-- Liste des entreprises -->
                    <?php if (empty($companies)): ?>
                        <div class="alert alert-info">
                            Aucune entreprise ne correspond à vos critères de recherche.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Ville</th>
                                        <th>Secteur</th>
                                        <th>Taille</th>
                                        <th>Note moyenne</th>
                                        <th>Offres</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($companies as $company): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($company['name']) ?></td>
                                            <td><?= htmlspecialchars($company['city']) ?></td>
                                            <td><?= htmlspecialchars($company['sector'] ?? 'Non spécifié') ?></td>
                                            <td><?= htmlspecialchars($company['size'] ?? 'Non spécifiée') ?></td>
                                            <td>
                                                <?php if ($company['average_rating']): ?>
                                                    <div class="rating">
                                                        <?= number_format($company['average_rating'], 1) ?>/5
                                                        <span class="text-warning">
                                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                <?php if ($i <= round($company['average_rating'])): ?>
                                                                    <i class="fas fa-star"></i>
                                                                <?php elseif ($i - 0.5 <= $company['average_rating']): ?>
                                                                    <i class="fas fa-star-half-alt"></i>
                                                                <?php else: ?>
                                                                    <i class="far fa-star"></i>
                                                                <?php endif; ?>
                                                            <?php endfor; ?>
                                                        </span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">Non évaluée</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?= (int)$company['offer_count'] ?></span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="index.php?page=companies&action=view&id=<?= $company['id'] ?>" class="btn btn-sm btn-primary" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($this->auth->hasPermission('SFx4')): ?>
                                                        <a href="index.php?page=companies&action=edit&id=<?= $company['id'] ?>" class="btn btn-sm btn-success" title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if ($this->auth->hasPermission('SFx6')): ?>
                                                        <a href="javascript:void(0);" onclick="confirmDelete(<?= $company['id'] ?>)" class="btn btn-sm btn-danger" title="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette entreprise ? Cette action est irréversible.</p>
                <p class="text-danger"><strong>Attention :</strong> Toutes les offres de stage associées seront également supprimées.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="#" id="confirmDeleteButton" class="btn btn-danger">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Fonction pour confirmer la suppression d'une entreprise
    function confirmDelete(id) {
        var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        document.getElementById('confirmDeleteButton').href = 'index.php?page=companies&action=delete&id=' + id;
        modal.show();
    }
</script>

<?php
// Inclusion du footer
require_once __DIR__ . '/../Templates/footer.php';
?>
