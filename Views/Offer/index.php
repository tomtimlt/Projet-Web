<?php include_once __DIR__ . '/../Templates/header.php'; ?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h2 class="mb-0"><?= $pageTitle ?></h2>
                    <div>
                        <?php if ($this->auth->hasRole(['admin', 'pilote'])): ?>
                            <a href="index.php?page=offers&action=statistics" class="btn btn-light me-2">
                                <i class="fas fa-chart-bar"></i> Statistiques
                            </a>
                        <?php endif; ?>
                        <?php if ($this->auth->hasPermission('SFx8') && !$this->auth->hasRole(['etudiant'])): ?>
                            <a href="index.php?page=offers&action=create" class="btn btn-light">
                                <i class="fas fa-plus"></i> Nouvelle offre
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtres de recherche -->
                    <form action="index.php" method="GET" id="searchForm" class="mb-4">
                        <input type="hidden" name="page" value="offers">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="company_id" class="form-label">Entreprise</label>
                                <select name="company_id" id="company_id" class="form-select search-trigger">
                                    <option value="">Toutes les entreprises</option>
                                    <?php foreach ($companies as $company): ?>
                                        <option value="<?= $company['id'] ?>" <?= ($filters['company_id'] == $company['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($company['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="location" class="form-label">Lieu</label>
                                <input type="text" name="location" id="location" class="form-control search-trigger" 
                                    value="<?= htmlspecialchars($filters['location']) ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Statut</label>
                                <select name="status" id="status" class="form-select search-trigger">
                                    <option value="">Tous les statuts</option>
                                    <option value="active" <?= ($filters['status'] == 'active') ? 'selected' : '' ?>>Actif</option>
                                    <option value="inactive" <?= ($filters['status'] == 'inactive') ? 'selected' : '' ?>>Inactif</option>
                                    <option value="filled" <?= ($filters['status'] == 'filled') ? 'selected' : '' ?>>Pourvu</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" class="btn btn-secondary w-100" id="resetSearch">
                                    <i class="fas fa-undo me-1"></i> Réinitialiser
                                </button>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Période</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="date" name="start_date" id="start_date" class="form-control search-trigger" 
                                            value="<?= htmlspecialchars($filters['start_date']) ?>" placeholder="Date de début">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="date" name="end_date" id="end_date" class="form-control search-trigger" 
                                            value="<?= htmlspecialchars($filters['end_date']) ?>" placeholder="Date de fin">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label mb-2">Compétences requises</label>
                                <div class="skills-container border rounded p-2" style="max-height: 100px; overflow-y: auto;">
                                    <?php foreach ($skills as $category => $categorySkills): ?>
                                        <div class="category-group mb-2">
                                            <strong><?= htmlspecialchars($category) ?></strong>:
                                            <div class="skills-group d-inline-block">
                                                <?php foreach ($categorySkills as $skill): ?>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input search-trigger" type="checkbox" name="skills[]" 
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
                        </div>
                    </form>

                    <?php if (empty($offers)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Aucune offre trouvée.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>Entreprise</th>
                                        <th>Lieu</th>
                                        <th>Dates</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($offers as $offer): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($offer['title']) ?></td>
                                            <td><?= htmlspecialchars($offer['company_name']) ?></td>
                                            <td><?= htmlspecialchars($offer['location']) ?></td>
                                            <td>
                                                <?php 
                                                    $startDate = new DateTime($offer['start_date']);
                                                    $endDate = new DateTime($offer['end_date']);
                                                    echo $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y');
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                    $statusLabels = [
                                                        'active' => '<span class="badge bg-success">Actif</span>',
                                                        'inactive' => '<span class="badge bg-secondary">Inactif</span>',
                                                        'filled' => '<span class="badge bg-info">Pourvu</span>'
                                                    ];
                                                    echo $statusLabels[$offer['status']] ?? '<span class="badge bg-secondary">-</span>';
                                                ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="index.php?page=offers&action=view&id=<?= $offer['id'] ?>" class="btn btn-outline-primary" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    <?php if ($this->auth->hasRole(['etudiant'])): ?>
                                                        <?php if ($offer['status'] === 'active'): ?>
                                                            <a href="index.php?page=applications&action=apply&offer_id=<?= $offer['id'] ?>" class="btn btn-outline-success" title="Postuler">
                                                                <i class="fas fa-paper-plane"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                        
                                                        <?php 
                                                            $inWishlist = isset($offer['in_wishlist']) ? $offer['in_wishlist'] : false;
                                                            $wishlistAction = $inWishlist 
                                                                ? 'remove-from-wishlist' 
                                                                : 'add-to-wishlist';
                                                            $wishlistIcon = $inWishlist 
                                                                ? 'fas fa-heart text-danger' 
                                                                : 'far fa-heart';
                                                            $wishlistTitle = $inWishlist 
                                                                ? 'Retirer des favoris' 
                                                                : 'Ajouter aux favoris';
                                                        ?>
                                                        <a href="index.php?page=wishlist&action=<?= $wishlistAction ?>&offer_id=<?= $offer['id'] ?>" 
                                                           class="btn btn-outline-info wishlist-btn" title="<?= $wishlistTitle ?>"
                                                           data-offer-id="<?= $offer['id'] ?>" data-in-wishlist="<?= $inWishlist ? '1' : '0' ?>">
                                                            <i class="<?= $wishlistIcon ?>"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($this->auth->hasPermission('SFx9')): ?>
                                                        <a href="index.php?page=offers&action=edit&id=<?= $offer['id'] ?>" class="btn btn-outline-warning" title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($this->auth->hasPermission('SFx10')): ?>
                                                        <button type="button" class="btn btn-outline-danger delete-offer" 
                                                                data-id="<?= $offer['id'] ?>" data-title="<?= htmlspecialchars($offer['title']) ?>" title="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
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
                            <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php
                                        // Calcul des pages à afficher
                                        $startPage = max(1, $currentPage - 2);
                                        $endPage = min($totalPages, $startPage + 4);
                                        
                                        // Ajustement pour toujours afficher 5 pages si possible
                                        if ($endPage - $startPage < 4 && $totalPages > 4) {
                                            $startPage = max(1, $endPage - 4);
                                        }
                                        
                                        // Première page
                                        if ($currentPage > 3) {
                                            echo '<li class="page-item"><a class="page-link" href="' . $this->buildPaginationUrl(1) . '"><i class="fas fa-angle-double-left"></i></a></li>';
                                        }
                                        
                                        // Page précédente
                                        if ($currentPage > 1) {
                                            echo '<li class="page-item"><a class="page-link" href="' . $this->buildPaginationUrl($currentPage - 1) . '"><i class="fas fa-angle-left"></i></a></li>';
                                        }
                                        
                                        // Pages numérotées
                                        for ($i = $startPage; $i <= $endPage; $i++) {
                                            $activeClass = ($i == $currentPage) ? 'active' : '';
                                            echo '<li class="page-item ' . $activeClass . '"><a class="page-link" href="' . $this->buildPaginationUrl($i) . '">' . $i . '</a></li>';
                                        }
                                        
                                        // Page suivante
                                        if ($currentPage < $totalPages) {
                                            echo '<li class="page-item"><a class="page-link" href="' . $this->buildPaginationUrl($currentPage + 1) . '"><i class="fas fa-angle-right"></i></a></li>';
                                        }
                                        
                                        // Dernière page
                                        if ($currentPage < $totalPages - 2) {
                                            echo '<li class="page-item"><a class="page-link" href="' . $this->buildPaginationUrl($totalPages) . '"><i class="fas fa-angle-double-right"></i></a></li>';
                                        }
                                    ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
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
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer l'offre <strong id="offerTitle"></strong> ?</p>
                <p class="text-danger">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la suppression
    const deleteModal = document.getElementById('deleteModal');
    const offerTitle = document.getElementById('offerTitle');
    const confirmDelete = document.getElementById('confirmDelete');
    const deleteButtons = document.querySelectorAll('.delete-offer');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const title = this.dataset.title;
            
            offerTitle.textContent = title;
            confirmDelete.href = `index.php?page=offers&action=delete&id=${id}`;
            
            const bsModal = new bootstrap.Modal(deleteModal);
            bsModal.show();
        });
    });

    // Recherche en temps réel
    const searchTriggers = document.querySelectorAll('.search-trigger');
    const searchForm = document.getElementById('searchForm');
    const resetButton = document.getElementById('resetSearch');
    let searchTimeout;

    // Détection des changements sur les éléments de filtre
    searchTriggers.forEach(trigger => {
        trigger.addEventListener('change', performSearch);
        if (trigger.type === 'text' || trigger.type === 'date') {
            trigger.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(performSearch, 500); // Délai de 500ms
            });
        }
    });

    // Réinitialisation des filtres
    resetButton.addEventListener('click', function() {
        searchForm.reset();
        performSearch();
    });

    // Exécution de la recherche
    function performSearch() {
        searchForm.submit();
    }
});
</script>

<?php include_once __DIR__ . '/../Templates/footer.php'; ?>
