<?php
$pageTitle = "Mes candidatures";
require_once __DIR__ . '/../Templates/header.php';
$auth = \Models\Auth::getInstance();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Mes candidatures</h1>
        <div>
            <a href="index.php?page=wishlist" class="btn btn-outline-primary me-2">
                <i class="fas fa-heart"></i> Ma liste de souhaits
            </a>
            <a href="index.php?page=offers" class="btn btn-primary">
                <i class="fas fa-search"></i> Rechercher des offres
            </a>
        </div>
    </div>

    <!-- Affichage des messages flash -->
    <?php require_once 'Views/Templates/flash.php'; ?>

    <!-- Statistiques rapides -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-chart-pie"></i> État de mes candidatures</h5>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="h1 mb-0 text-primary"><?= $stats['total'] ?></div>
                    <div class="small text-muted">Total</div>
                </div>
                <div class="col-md-3">
                    <div class="h1 mb-0 text-warning"><?= $stats['pending'] ?></div>
                    <div class="small text-muted">En attente</div>
                </div>
                <div class="col-md-3">
                    <div class="h1 mb-0 text-success"><?= $stats['accepted'] ?></div>
                    <div class="small text-muted">Acceptées</div>
                </div>
                <div class="col-md-3">
                    <div class="h1 mb-0 text-danger"><?= $stats['rejected'] + $stats['withdrawn'] ?></div>
                    <div class="small text-muted">Refusées/Annulées</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des candidatures -->
    <?php if (empty($applications)) : ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Vous n'avez pas encore de candidatures.
            <a href="index.php?page=offers" class="alert-link">Cliquez ici pour rechercher des offres</a>.
        </div>
    <?php else : ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Offre</th>
                        <th>Entreprise</th>
                        <th>Date de candidature</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $application) : ?>
                        <tr>
                            <td><?= htmlspecialchars($application['offer_title']) ?></td>
                            <td><?= htmlspecialchars($application['company_name']) ?></td>
                            <td><?= date('d/m/Y', strtotime($application['created_at'])) ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    switch($application['status']) {
                                        case 'pending': echo 'warning'; break;
                                        case 'accepted': echo 'success'; break;
                                        case 'rejected': echo 'danger'; break;
                                        case 'withdrawn': echo 'secondary'; break;
                                        default: echo 'info';
                                    }
                                ?>">
                                    <?php 
                                        switch($application['status']) {
                                            case 'pending': echo 'En attente'; break;
                                            case 'accepted': echo 'Acceptée'; break;
                                            case 'rejected': echo 'Refusée'; break;
                                            case 'withdrawn': echo 'Annulée'; break;
                                            default: echo $application['status'];
                                        }
                                    ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="index.php?page=applications&action=view&id=<?= $application['id'] ?>" class="btn btn-sm btn-info" title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($application['status'] === 'pending') : ?>
                                        <a href="index.php?page=applications&action=cancel&id=<?= $application['id'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Voulez-vous vraiment annuler cette candidature ?')" 
                                           title="Annuler">
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

<?php require_once 'Views/Templates/footer.php'; ?>
