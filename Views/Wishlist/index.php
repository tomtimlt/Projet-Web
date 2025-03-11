<?php
$pageTitle = "Ma liste de souhaits";
require_once __DIR__ . '/../Templates/header.php';
$auth = \Models\Auth::getInstance();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Ma liste de souhaits</h1>
        <a href="index.php?page=offers" class="btn btn-primary">
            <i class="fas fa-search"></i> Rechercher des offres
        </a>
    </div>

    <!-- Affichage des messages flash -->
    <?php require_once 'Views/Templates/flash.php'; ?>

    <!-- Statistiques rapides -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Mes statistiques</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <div class="h1 mb-0 text-primary"><?= count($wishlist) ?></div>
                    <div class="small text-muted">Offres en favoris</div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="h1 mb-0 text-success"><?= $stats['total'] ?></div>
                    <div class="small text-muted">Candidatures totales</div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="h1 mb-0 text-warning"><?= $stats['pending'] ?></div>
                    <div class="small text-muted">En attente</div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="h1 mb-0 text-info"><?= $stats['accepted'] ?></div>
                    <div class="small text-muted">Acceptées</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des offres en wishlist -->
    <?php if (empty($wishlist)) : ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Vous n'avez aucune offre dans votre liste de souhaits.
            <a href="index.php?page=offers" class="alert-link">Cliquez ici pour rechercher des offres</a>.
        </div>
    <?php else : ?>
        <div class="row">
            <?php foreach ($wishlist as $offer) : ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><?= htmlspecialchars($offer['title']) ?></h5>
                            <span class="badge bg-<?= $offer['status'] === 'active' ? 'success' : 'secondary' ?>">
                                <?= $offer['status'] === 'active' ? 'Active' : 'Inactive' ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong><i class="fas fa-building"></i> Entreprise:</strong> 
                                <?= htmlspecialchars($offer['company_name'] ?? 'Non spécifiée') ?>
                            </div>
                            <div class="mb-3">
                                <strong><i class="fas fa-map-marker-alt"></i> Lieu:</strong> 
                                <?= htmlspecialchars($offer['location'] ?? 'Non spécifié') ?>
                            </div>
                            <div class="mb-3">
                                <strong><i class="fas fa-calendar-alt"></i> Période:</strong> 
                                <?= isset($offer['start_date']) ? date('d/m/Y', strtotime($offer['start_date'])) : 'Non spécifiée' ?> - 
                                <?= isset($offer['end_date']) ? date('d/m/Y', strtotime($offer['end_date'])) : 'Non spécifiée' ?>
                            </div>
                            <div class="mb-3">
                                <strong><i class="fas fa-euro-sign"></i> Rémunération:</strong> 
                                <?= isset($offer['salary']) && $offer['salary'] ? htmlspecialchars($offer['salary']) . ' €/mois' : 'Non précisée' ?>
                            </div>
                            <p class="card-text">
                                <?= isset($offer['description']) ? nl2br(htmlspecialchars(substr($offer['description'], 0, 150))) : 'Aucune description disponible' ?>
                                <?= isset($offer['description']) && strlen($offer['description']) > 150 ? '...' : '' ?>
                            </p>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <div>
                                <a href="index.php?page=offers&action=view&id=<?= $offer['offer_id'] ?>" class="btn btn-sm btn-info me-2">
                                    <i class="fas fa-eye"></i> Détails
                                </a>
                                <?php if ($offer['status'] === 'active') : ?>
                                    <a href="index.php?page=applications&action=apply&id=<?= $offer['offer_id'] ?>" class="btn btn-sm btn-success">
                                        <i class="fas fa-paper-plane"></i> Postuler
                                    </a>
                                <?php endif; ?>
                            </div>
                            <a href="index.php?page=wishlist&action=remove&id=<?= $offer['offer_id'] ?>" class="btn btn-sm btn-danger">
                                <i class="fas fa-heart-broken"></i> Retirer
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'Views/Templates/footer.php'; ?>
