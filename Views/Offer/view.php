<?php include_once __DIR__ . '/../Templates/header.php'; ?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?page=offers">Offres de stage</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($offer['title']) ?></li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= htmlspecialchars($offer['title']) ?></h1>
        <div>
            <?php if ($this->auth->hasPermission('SFx9')): ?>
                <a href="index.php?page=offers&action=edit&id=<?= $offer['id'] ?>" class="btn btn-outline-primary me-2">
                    <i class="fas fa-edit me-1"></i>Modifier
                </a>
            <?php endif; ?>
            <?php if ($this->auth->hasPermission('SFx10')): ?>
                <button type="button" class="btn btn-outline-danger delete-offer" 
                        data-id="<?= $offer['id'] ?>" data-title="<?= htmlspecialchars($offer['title']) ?>">
                    <i class="fas fa-trash me-1"></i>Supprimer
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Colonne principale -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Détails de l'offre</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
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
                        <span class="badge <?= $statusClass ?> mb-2"><?= $statusText ?></span>
                        
                        <h5>Description</h5>
                        <div class="mb-3 description-content">
                            <?= nl2br(htmlspecialchars($offer['description'])) ?>
                        </div>
                        
                        <?php if (!empty($offer['skills_required'])): ?>
                            <h5>Compétences requises</h5>
                            <div class="mb-3">
                                <?= nl2br(htmlspecialchars($offer['skills_required'])) ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-flex flex-wrap mt-4">
                            <h5 class="w-100 mb-2">Compétences recherchées</h5>
                            <?php if (!empty($offer['skills'])): ?>
                                <?php foreach ($offer['skills'] as $skill): ?>
                                    <span class="badge bg-light text-dark border me-2 mb-2 p-2">
                                        <?= htmlspecialchars($skill['name']) ?>
                                        <?php if (!empty($skill['category'])): ?>
                                            <small class="text-muted">(<?= htmlspecialchars($skill['category']) ?>)</small>
                                        <?php endif; ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">Aucune compétence spécifique renseignée</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <h5>Période de stage</h5>
                            <p>
                                <i class="far fa-calendar-alt me-2"></i>
                                Du <?= date('d/m/Y', strtotime($offer['start_date'])) ?> 
                                au <?= date('d/m/Y', strtotime($offer['end_date'])) ?>
                                <small class="text-muted">
                                    (<?= ceil((strtotime($offer['end_date']) - strtotime($offer['start_date'])) / (60 * 60 * 24 * 7)) ?> semaines)
                                </small>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5>Lieu</h5>
                            <p><i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($offer['location']) ?></p>
                        </div>
                        
                        <?php if (!empty($offer['salary'])): ?>
                        <div class="col-md-6">
                            <h5>Gratification</h5>
                            <p><i class="fas fa-euro-sign me-2"></i><?= number_format($offer['salary'], 2, ',', ' ') ?> € / mois</p>
                        </div>
                        <?php endif; ?>
                        
                        <div class="col-md-6">
                            <h5>Candidatures</h5>
                            <p>
                                <i class="fas fa-users me-2"></i>
                                <?= $offer['application_count'] ?> candidature(s)
                                <?php if ($this->auth->hasPermission('SFx12') && $offer['application_count'] > 0): ?>
                                    <a href="index.php?page=applications&offer_id=<?= $offer['id'] ?>" class="btn btn-sm btn-outline-primary ms-2">
                                        Voir les candidatures
                                    </a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <?php
                    // Get application and wishlist models
                    $applicationModel = new \Models\Application();
                    $wishlistModel = new \Models\Wishlist();
                    
                    // Check if user is logged in
                    $isLoggedIn = $this->auth->isLoggedIn();
                    $userId = $isLoggedIn ? $this->auth->getUserId() : 0;
                    
                    // Check if the student has already applied for this offer
                    $hasApplied = $isLoggedIn ? $applicationModel->hasApplied($userId, $offer['id']) : false;
                    
                    // Check if the offer is in the student's wishlist
                    $isInWishlist = $isLoggedIn ? $wishlistModel->isInWishlist($userId, $offer['id']) : false;
                    ?>
                    
                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 mt-4">
                        <div class="row">
                            <!-- Apply button - Always visible for logged-in users -->
                            <?php if ($isLoggedIn): ?>
                            <div class="col-md-6 mb-2">
                                <?php if (!$hasApplied): ?>
                                    <a href="index.php?page=applications&action=apply&id=<?= $offer['id'] ?>" class="btn btn-primary w-100">
                                        <i class="fas fa-paper-plane me-2"></i>Postuler
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-success w-100" disabled>
                                        <i class="fas fa-check me-2"></i>Candidature envoyée
                                    </button>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Wishlist button -->
                            <div class="col-md-6 mb-2">
                                <?php if (!$isInWishlist): ?>
                                    <a href="index.php?page=wishlist&action=add&id=<?= $offer['id'] ?>" class="btn btn-outline-warning w-100">
                                        <i class="far fa-heart me-2"></i>Ajouter à la wishlist
                                    </a>
                                <?php else: ?>
                                    <a href="index.php?page=wishlist&action=remove&id=<?= $offer['id'] ?>" class="btn btn-warning w-100">
                                        <i class="fas fa-heart me-2"></i>Retirer de la wishlist
                                    </a>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <!-- Button for non-logged in users -->
                            <div class="col-12 mb-2">
                                <a href="index.php?page=login" class="btn btn-primary w-100">
                                    <i class="fas fa-sign-in-alt me-2"></i>Connectez-vous pour postuler
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Colonne latérale -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>Entreprise</h5>
                </div>
                <div class="card-body">
                    <h4><?= htmlspecialchars($offer['company_name']) ?></h4>
                    
                    <p class="mb-3">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <?= htmlspecialchars($offer['company_address']) ?>, <?= htmlspecialchars($offer['company_city']) ?>
                    </p>
                    
                    <?php if (!empty($offer['company_email'])): ?>
                        <p class="mb-3">
                            <i class="fas fa-envelope me-2"></i>
                            <a href="mailto:<?= htmlspecialchars($offer['company_email']) ?>"><?= htmlspecialchars($offer['company_email']) ?></a>
                        </p>
                    <?php endif; ?>
                    
                    <?php if (!empty($offer['company_website'])): ?>
                        <p class="mb-3">
                            <i class="fas fa-globe me-2"></i>
                            <a href="<?= htmlspecialchars($offer['company_website']) ?>" target="_blank"><?= htmlspecialchars($offer['company_website']) ?></a>
                        </p>
                    <?php endif; ?>
                    
                    <a href="index.php?page=company_view&id=<?= $offer['company_id'] ?>" class="btn btn-outline-primary btn-sm mt-2">
                        <i class="fas fa-info-circle me-1"></i>Voir le profil complet
                    </a>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Informations</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Date de création</span>
                            <span>
                                <?= !empty($offer['created_at']) ? date('d/m/Y', strtotime($offer['created_at'])) : 'Non renseignée' ?>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Dernière mise à jour</span>
                            <span>
                                <?= !empty($offer['updated_at']) ? date('d/m/Y', strtotime($offer['updated_at'])) : 'Non renseignée' ?>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-share-alt me-2"></i>Partager</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-around">
                        <a href="mailto:?subject=<?= urlencode('Offre de stage : ' . $offer['title']) ?>&body=<?= urlencode('Voici une offre de stage intéressante : ' . $offer['title'] . "\n\n" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-envelope"></i>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" target="_blank" class="btn btn-outline-secondary">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text=<?= urlencode('Offre de stage : ' . $offer['title']) ?>&url=<?= urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" target="_blank" class="btn btn-outline-secondary">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($this->auth->hasRole(['admin'])): ?>
    <!-- Section d'administration - Seulement visible pour les administrateurs -->
    <div class="mt-5 border-top pt-4">
        <h4><i class="fas fa-user-shield me-2"></i>Administration</h4>
        
        <!-- Onglets -->
        <ul class="nav nav-tabs" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="applications-tab" data-bs-toggle="tab" data-bs-target="#applications" type="button" role="tab" aria-controls="applications" aria-selected="true">
                    <i class="fas fa-paper-plane me-1"></i>Candidatures
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="wishlists-tab" data-bs-toggle="tab" data-bs-target="#wishlists" type="button" role="tab" aria-controls="wishlists" aria-selected="false">
                    <i class="fas fa-heart me-1"></i>Wishlists
                </button>
            </li>
        </ul>
        
        <!-- Contenu des onglets -->
        <div class="tab-content" id="adminTabsContent">
            <!-- Onglet Candidatures -->
            <div class="tab-pane fade show active p-3" id="applications" role="tabpanel" aria-labelledby="applications-tab">
                <?php
                // Récupérer les candidatures pour cette offre
                $applications = $applicationModel->getApplicationsByOfferId($offer['id']);
                ?>
                
                <?php if (empty($applications)): ?>
                    <div class="alert alert-info">
                        Aucun étudiant n'a encore postulé à cette offre.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Étudiant</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($applications as $application): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($application['firstname'] . ' ' . $application['lastname']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($application['created_at'])) ?></td>
                                        <td>
                                            <?php 
                                            $statusClass = [
                                                'pending' => 'bg-warning',
                                                'accepted' => 'bg-success',
                                                'rejected' => 'bg-danger'
                                            ][$application['status']] ?? 'bg-secondary';
                                            
                                            $statusLabel = [
                                                'pending' => 'En attente',
                                                'accepted' => 'Acceptée',
                                                'rejected' => 'Refusée'
                                            ][$application['status']] ?? 'Inconnu';
                                            ?>
                                            <span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="index.php?page=applications&action=view&id=<?= $application['id'] ?>" class="btn btn-outline-primary" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($application['status'] === 'pending'): ?>
                                                    <a href="index.php?page=applications&action=update&id=<?= $application['id'] ?>&status=accepted" class="btn btn-outline-success" title="Accepter">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                    <a href="index.php?page=applications&action=update&id=<?= $application['id'] ?>&status=rejected" class="btn btn-outline-danger" title="Refuser">
                                                        <i class="fas fa-times"></i>
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
            
            <!-- Onglet Wishlists -->
            <div class="tab-pane fade p-3" id="wishlists" role="tabpanel" aria-labelledby="wishlists-tab">
                <?php
                // Récupérer les étudiants qui ont ajouté cette offre à leur wishlist
                $wishlists = $wishlistModel->getWishlistsByOfferId($offer['id']);
                ?>
                
                <?php if (empty($wishlists)): ?>
                    <div class="alert alert-info">
                        Aucun étudiant n'a encore ajouté cette offre à sa wishlist.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Étudiant</th>
                                    <th>Date d'ajout</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($wishlists as $wishlist): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($wishlist['firstname'] . ' ' . $wishlist['lastname']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($wishlist['created_at'])) ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="index.php?page=wishlist&student_id=<?= $wishlist['user_id'] ?>" class="btn btn-outline-primary" title="Voir la wishlist">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="index.php?page=wishlist&action=remove&id=<?= $offer['id'] ?>&student_id=<?= $wishlist['user_id'] ?>" class="btn btn-outline-danger" title="Retirer de la wishlist">
                                                    <i class="fas fa-trash"></i>
                                                </a>
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
    <?php endif; ?>
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
    // Gestion de la confirmation de suppression
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    
    document.querySelectorAll('.delete-offer').forEach(function(button) {
        button.addEventListener('click', function() {
            const offerId = this.getAttribute('data-id');
            const offerTitle = this.getAttribute('data-title');
            
            document.getElementById('offerTitle').textContent = offerTitle;
            document.getElementById('deleteLink').href = 'index.php?page=offer&action=delete&id=' + offerId;
            
            deleteModal.show();
        });
    });
});
</script>

<?php include_once __DIR__ . '/../Templates/footer.php'; ?>
