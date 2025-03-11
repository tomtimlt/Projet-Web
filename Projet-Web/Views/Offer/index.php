<?php include_once __DIR__ . '/../Templates/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= $pageTitle ?></h1>
        <div>
            <?php if ($this->auth->hasRole(['admin', 'pilote'])): ?>
                <a href="index.php?page=offers&action=statistics" class="btn btn-info me-2">
                    <i class="fas fa-chart-bar"></i> Statistiques
                </a>
            <?php endif; ?>
            <?php if ($this->auth->hasPermission('SFx8') && !$this->auth->hasRole(['etudiant'])): ?>
                <a href="index.php?page=offers&action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouvelle offre
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Carte de recherche/filtrage -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-search me-2"></i>Rechercher des offres</h5>
        </div>
        <div class="card-body">
            <form action="index.php" method="GET" id="searchForm">
                <input type="hidden" name="page" value="offers">
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="keyword">Mots-clés</label>
                            <input type="text" name="keyword" id="keyword" class="form-control" 
                                value="<?= htmlspecialchars($filters['keyword']) ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="company_id">Entreprise</label>
                            <select name="company_id" id="company_id" class="form-select">
                                <option value="">Toutes les entreprises</option>
                                <?php foreach ($companies as $company): ?>
                                    <option value="<?= $company['id'] ?>" <?= ($filters['company_id'] == $company['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($company['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="location">Lieu</label>
                            <input type="text" name="location" id="location" class="form-control" 
                                value="<?= htmlspecialchars($filters['location']) ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="start_date">Date de début (à partir de)</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" 
                                value="<?= htmlspecialchars($filters['start_date']) ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="end_date">Date de fin (jusqu'à)</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" 
                                value="<?= htmlspecialchars($filters['end_date']) ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Statut</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">Tous les statuts</option>
                                <option value="active" <?= ($filters['status'] == 'active') ? 'selected' : '' ?>>Actif</option>
                                <option value="inactive" <?= ($filters['status'] == 'inactive') ? 'selected' : '' ?>>Inactif</option>
                                <option value="filled" <?= ($filters['status'] == 'filled') ? 'selected' : '' ?>>Pourvu</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label>Compétences requises</label>
                    <div class="skills-container">
                        <?php foreach ($skills as $category => $categorySkills): ?>
                            <div class="category-group mb-2">
                                <h6><?= htmlspecialchars($category) ?></h6>
                                <div class="skills-group">
                                    <?php foreach ($categorySkills as $skill): ?>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="skills[]" 
                                                   id="skill_<?= $skill['id'] ?>" value="<?= $skill['id'] ?>"
                                                   <?= in_array($skill['id'], $filters['skills']) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="skill_<?= $skill['id'] ?>">
                                                <?= htmlspecialchars($skill['name']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-outline-secondary me-2" id="resetSearch">
                        <i class="fas fa-undo me-1"></i>Réinitialiser
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>Rechercher
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Résultats de la recherche -->
    <div class="card">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Résultats</h5>
                <span class="badge bg-primary"><?= $totalOffers ?> offre(s) trouvée(s)</span>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($offers)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Aucune offre trouvée.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Titre</th>
                                <th>Entreprise</th>
                                <th>Lieu</th>
                                <th>Dates</th>
                                <th>Statut</th>
                                <th>Candidatures</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($offers as $offer): ?>
                                <tr>
                                    <td>
                                        <a href="index.php?page=offers&action=view&id=<?= $offer['id'] ?>" class="fw-bold text-decoration-none">
                                            <?= htmlspecialchars($offer['title']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($offer['company_name']) ?></td>
                                    <td><?= htmlspecialchars($offer['location']) ?></td>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('d/m/Y', strtotime($offer['start_date'])) ?> - 
                                            <?= date('d/m/Y', strtotime($offer['end_date'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = 'bg-secondary';
                                        $statusText = 'Inconnu';
                                        
                                        switch($offer['status']) {
                                            case 'active':
                                                $statusClass = 'bg-success';
                                                $statusText = 'Actif';
                                                break;
                                            case 'inactive':
                                                $statusClass = 'bg-warning';
                                                $statusText = 'Inactif';
                                                break;
                                            case 'filled':
                                                $statusClass = 'bg-info';
                                                $statusText = 'Pourvu';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-users me-1"></i><?= $offer['application_count'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="index.php?page=offers&action=view&id=<?= $offer['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($this->auth->hasPermission('SFx9')): ?>
                                                <a href="index.php?page=offers&action=edit&id=<?= $offer['id'] ?>" 
                                                   class="btn btn-sm btn-outline-secondary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($this->auth->hasPermission('SFx10')): ?>
                                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger delete-offer" 
                                                   data-id="<?= $offer['id'] ?>" data-title="<?= htmlspecialchars($offer['title']) ?>" title="Supprimer">
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
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=offers&page_num=<?= ($page - 1) ?><?= http_build_query(array_filter($filters)) ? '&' . http_build_query(array_filter($filters)) : '' ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $page + 2);
                            
                            if ($startPage > 1) {
                                echo '<li class="page-item"><a class="page-link" href="?page=offers&page_num=1' . (http_build_query(array_filter($filters)) ? '&' . http_build_query(array_filter($filters)) : '') . '">1</a></li>';
                                if ($startPage > 2) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                            }
                            
                            for ($i = $startPage; $i <= $endPage; $i++) {
                                echo '<li class="page-item ' . (($i == $page) ? 'active' : '') . '">
                                      <a class="page-link" href="?page=offers&page_num=' . $i . (http_build_query(array_filter($filters)) ? '&' . http_build_query(array_filter($filters)) : '') . '">' . $i . '</a>
                                      </li>';
                            }
                            
                            if ($endPage < $totalPages) {
                                if ($endPage < $totalPages - 1) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                                echo '<li class="page-item"><a class="page-link" href="?page=offers&page_num=' . $totalPages . (http_build_query(array_filter($filters)) ? '&' . http_build_query(array_filter($filters)) : '') . '">' . $totalPages . '</a></li>';
                            }
                            ?>
                            
                            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=offers&page_num=<?= ($page + 1) ?><?= http_build_query(array_filter($filters)) ? '&' . http_build_query(array_filter($filters)) : '' ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer l'offre "<span id="offerTitle"></span>" ?</p>
                <p class="text-danger"><strong>Attention :</strong> Cette action est irréversible et supprimera également toutes les candidatures associées.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="#" id="deleteLink" class="btn btn-danger">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du formulaire de recherche
    document.getElementById('resetSearch').addEventListener('click', function() {
        // Réinitialiser tous les champs du formulaire
        document.getElementById('searchForm').reset();
        
        // Rediriger vers la page sans filtres
        window.location.href = 'index.php?page=offers';
    });
    
    // Gestion de la confirmation de suppression
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    
    document.querySelectorAll('.delete-offer').forEach(function(button) {
        button.addEventListener('click', function() {
            const offerId = this.getAttribute('data-id');
            const offerTitle = this.getAttribute('data-title');
            
            document.getElementById('offerTitle').textContent = offerTitle;
            document.getElementById('deleteLink').href = 'index.php?page=offers&action=delete&id=' + offerId;
            
            deleteModal.show();
        });
    });
});
</script>

<?php include_once __DIR__ . '/../Templates/footer.php'; ?>
