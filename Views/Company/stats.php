<?php
// Titre de la page
$pageTitle = "Statistiques des entreprises";

// Inclusion du header
require_once __DIR__ . '/../Templates/header.php';
?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Statistiques des entreprises</h2>
                <a href="index.php?page=companies" class="btn btn-light">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card bg-primary text-white text-center h-100">
                            <div class="card-body">
                                <h5 class="card-title">Nombre total d'entreprises</h5>
                                <p class="display-4"><?= $statistics['total_count'] ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Répartition par taille -->
                    <div class="col-md-9 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">Répartition par taille</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php
                                    $sizes = ['TPE', 'PME', 'ETI', 'GE', null];
                                    $sizeLabels = [
                                        'TPE' => 'Très Petite Entreprise',
                                        'PME' => 'Petite et Moyenne Entreprise',
                                        'ETI' => 'Entreprise de Taille Intermédiaire',
                                        'GE' => 'Grande Entreprise',
                                        null => 'Non spécifiée'
                                    ];
                                    $colors = [
                                        'TPE' => 'success',
                                        'PME' => 'info',
                                        'ETI' => 'warning',
                                        'GE' => 'danger',
                                        null => 'secondary'
                                    ];
                                    
                                    $sizeData = [];
                                    foreach ($statistics['size_distribution'] as $size) {
                                        $sizeData[$size['size']] = $size['count'];
                                    }
                                    
                                    // Calculer le pourcentage pour chaque taille
                                    $totalCount = $statistics['total_count'] > 0 ? $statistics['total_count'] : 1; // Éviter division par zéro
                                    
                                    foreach ($sizes as $size) {
                                        $count = $sizeData[$size] ?? 0;
                                        $percentage = round(($count / $totalCount) * 100, 1);
                                        $label = $sizeLabels[$size];
                                        $color = $colors[$size];
                                    ?>
                                        <div class="col-md-4 col-6 mb-3">
                                            <div class="card border-<?= $color ?> h-100">
                                                <div class="card-body text-center">
                                                    <h6 class="card-title"><?= htmlspecialchars($label) ?></h6>
                                                    <div class="display-5 fw-bold text-<?= $color ?>"><?= $count ?></div>
                                                    <div class="progress mt-2">
                                                        <div class="progress-bar bg-<?= $color ?>" role="progressbar" 
                                                             style="width: <?= $percentage ?>%" 
                                                             aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100">
                                                            <?= $percentage ?>%
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Top des entreprises avec le plus d'offres -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">Top 5 des entreprises avec le plus d'offres</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($statistics['top_companies'])): ?>
                                    <p class="text-muted">Aucune entreprise n'a d'offres pour le moment.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Rang</th>
                                                    <th>Entreprise</th>
                                                    <th>Offres</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($statistics['top_companies'] as $i => $company): ?>
                                                <tr>
                                                    <td><span class="badge bg-success"><?= $i + 1 ?></span></td>
                                                    <td>
                                                        <a href="index.php?page=companies&action=view&id=<?= $company['id'] ?>">
                                                            <?= htmlspecialchars($company['name']) ?>
                                                        </a>
                                                    </td>
                                                    <td><span class="badge bg-primary"><?= $company['offer_count'] ?></span></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Top des entreprises les mieux notées -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">Top 5 des entreprises les mieux notées</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($statistics['top_rated'])): ?>
                                    <p class="text-muted">Aucune entreprise n'a été évaluée pour le moment.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Rang</th>
                                                    <th>Entreprise</th>
                                                    <th>Note moyenne</th>
                                                    <th>Évaluations</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($statistics['top_rated'] as $i => $company): ?>
                                                <tr>
                                                    <td><span class="badge bg-warning text-dark"><?= $i + 1 ?></span></td>
                                                    <td>
                                                        <a href="index.php?page=companies&action=view&id=<?= $company['id'] ?>">
                                                            <?= htmlspecialchars($company['name']) ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div class="rating">
                                                            <?= number_format($company['avg_rating'], 1) ?>/5
                                                            <span class="text-warning">
                                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                    <?php if ($i <= round($company['avg_rating'])): ?>
                                                                        <i class="fas fa-star"></i>
                                                                    <?php elseif ($i - 0.5 <= $company['avg_rating']): ?>
                                                                        <i class="fas fa-star-half-alt"></i>
                                                                    <?php else: ?>
                                                                        <i class="far fa-star"></i>
                                                                    <?php endif; ?>
                                                                <?php endfor; ?>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td><span class="badge bg-info"><?= $company['rating_count'] ?></span></td>
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
        </div>
    </div>
</div>

<?php
// Inclusion du footer
require_once __DIR__ . '/../Templates/footer.php';
?>
