<?php include_once __DIR__ . '/../Templates/header.php'; ?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?page=pilots">Gestion des pilotes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Statistiques</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-chart-bar me-2"></i><?= $pageTitle ?></h1>
        <a href="index.php?page=pilots" class="btn btn-primary">
            <i class="fas fa-arrow-left me-1"></i>Retour à la liste
        </a>
    </div>

    <div class="row">
        <!-- Résumé des statistiques -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Résumé</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="display-4 text-primary me-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <p class="lead mb-0"><?= $statistics['total_pilots'] ?></p>
                            <h5>Pilotes enregistrés</h5>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <p class="text-muted">Les pilotes sont responsables de la gestion des entreprises et des offres associées. Cette page fournit une vue d'ensemble des statistiques relatives aux pilotes.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pilotes par nombre d'entreprises -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>Top pilotes par nombre d'entreprises</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($statistics['pilots_by_companies'])): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Aucune donnée disponible.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Pilote</th>
                                        <th class="text-center">Nombre d'entreprises</th>
                                        <th class="text-center">Proportion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($statistics['pilots_by_companies'] as $pilot): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($pilot['firstname'] . ' ' . $pilot['lastname']) ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-primary"><?= $pilot['company_count'] ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                    $percentage = ($statistics['total_pilots'] > 0) 
                                                        ? round(($pilot['company_count'] / max(1, array_sum(array_column($statistics['pilots_by_companies'], 'company_count')))) * 100, 2) 
                                                        : 0;
                                                ?>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: <?= $percentage ?>%;" 
                                                         aria-valuenow="<?= $percentage ?>" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        <?= $percentage ?>%
                                                    </div>
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
    
    <!-- Graphiques supplémentaires pourraient être ajoutés ici -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Analyse des activités</h5>
        </div>
        <div class="card-body">
            <p class="text-muted text-center mb-0">
                Des graphiques d'analyse supplémentaires seront disponibles dans les futures mises à jour.
            </p>
        </div>
    </div>
</div>

<!-- Ajout de Chart.js pour les futurs graphiques -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php include_once __DIR__ . '/../Templates/footer.php'; ?>
