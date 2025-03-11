<?php
$pageTitle = "Détails de la candidature";
require_once __DIR__ . '/../Templates/header.php';
$auth = \Models\Auth::getInstance();
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?page=applications">Mes candidatures</a></li>
            <li class="breadcrumb-item active" aria-current="page">Détails</li>
        </ol>
    </nav>

    <!-- Affichage des messages flash -->
    <?php require_once 'Views/Templates/flash.php'; ?>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h1 class="h4 mb-0">Candidature pour : <?= htmlspecialchars($application['offer_title']) ?></h1>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="border-bottom pb-2 mb-3">Informations sur l'offre</h5>
                    <p><strong><i class="fas fa-building"></i> Entreprise :</strong> <?= htmlspecialchars($application['company_name']) ?></p>
                    <p><strong><i class="fas fa-map-marker-alt"></i> Lieu :</strong> <?= htmlspecialchars($application['location'] ?? 'Non spécifié') ?></p>
                    <p><strong><i class="fas fa-calendar-alt"></i> Période :</strong> 
                        <?= isset($application['start_date']) ? date('d/m/Y', strtotime($application['start_date'])) : 'Non spécifiée' ?> - 
                        <?= isset($application['end_date']) ? date('d/m/Y', strtotime($application['end_date'])) : 'Non spécifiée' ?>
                    </p>
                </div>
                <div class="col-md-6">
                    <h5 class="border-bottom pb-2 mb-3">Informations sur la candidature</h5>
                    <p><strong><i class="fas fa-clock"></i> Date de candidature :</strong> <?= date('d/m/Y à H:i', strtotime($application['created_at'])) ?></p>
                    <p><strong><i class="fas fa-sync-alt"></i> Dernière mise à jour :</strong> <?= date('d/m/Y à H:i', strtotime($application['updated_at'])) ?></p>
                    <p>
                        <strong><i class="fas fa-tag"></i> Statut :</strong> 
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
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-file-alt"></i> Lettre de motivation</h5>
                </div>
                <div class="card-body">
                    <div class="motivation-text bg-light p-3 rounded">
                        <?= nl2br(htmlspecialchars($application['motivation'])) ?>
                    </div>
                </div>
            </div>

            <?php if ($application['cv_file']) : ?>
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-file-pdf"></i> CV</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">Vous pouvez consulter le CV joint à cette candidature.</p>
                        <a href="<?= htmlspecialchars($application['cv_file']) ?>" class="btn btn-primary" target="_blank">
                            <i class="fas fa-download"></i> Télécharger le CV
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between mt-4">
                <a href="index.php?page=applications" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
                <?php if ($auth->hasRole(['etudiant']) && $application['status'] === 'pending') : ?>
                    <a href="index.php?page=applications&action=cancel&id=<?= $application['id'] ?>" 
                       class="btn btn-danger" 
                       onclick="return confirm('Voulez-vous vraiment annuler cette candidature ?')">
                        <i class="fas fa-times"></i> Annuler ma candidature
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'Views/Templates/footer.php'; ?>
