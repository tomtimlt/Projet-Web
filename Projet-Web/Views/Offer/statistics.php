<?php require_once __DIR__ . '/../Templates/header.php'; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">
            <i class="fas fa-chart-bar me-2"></i>
            <?php echo $pageTitle; ?>
        </h1>
        <a href="index.php?page=offers" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Retour aux offres
        </a>
    </div>

    <!-- Cartes de statistiques générales -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <h3 class="display-4 text-primary"><?= $stats['totalOffers'] ?></h3>
                    <h5 class="card-title">Total des offres</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <h3 class="display-4 text-success"><?= $stats['activeOffers'] ?></h3>
                    <h5 class="card-title">Offres actives</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <h3 class="display-4 text-warning"><?= $stats['inactiveOffers'] ?></h3>
                    <h5 class="card-title">Offres inactives</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <h3 class="display-4 text-info"><?= $stats['filledOffers'] ?></h3>
                    <h5 class="card-title">Offres pourvues</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques par période -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="card-title"><i class="fas fa-calendar-plus me-2"></i>Offres à venir</h4>
                    <hr>
                    <div class="text-center">
                        <h3 class="display-5 text-success my-4"><?= $stats['upcomingOffers'] ?></h3>
                        <p class="card-text text-muted">
                            Offres dont la date de début est dans le futur
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="card-title"><i class="fas fa-calendar-check me-2"></i>Offres en cours</h4>
                    <hr>
                    <div class="text-center">
                        <h3 class="display-5 text-primary my-4"><?= $stats['currentOffers'] ?></h3>
                        <p class="card-text text-muted">
                            Offres actuellement disponibles
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="card-title"><i class="fas fa-calendar-times me-2"></i>Offres passées</h4>
                    <hr>
                    <div class="text-center">
                        <h3 class="display-5 text-secondary my-4"><?= $stats['pastOffers'] ?></h3>
                        <p class="card-text text-muted">
                            Offres dont la date de fin est passée
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Statistiques par entreprise -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title mb-0"><i class="fas fa-building me-2"></i>Offres par entreprise</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($stats['offersByCompany'])): ?>
                        <div class="alert alert-info">Aucune donnée disponible</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Entreprise</th>
                                        <th class="text-end">Nombre d'offres</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stats['offersByCompany'] as $company): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($company['name']) ?></td>
                                            <td class="text-end"><?= $company['offer_count'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Statistiques par lieu -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title mb-0"><i class="fas fa-map-marker-alt me-2"></i>Offres par lieu</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($stats['offersByLocation'])): ?>
                        <div class="alert alert-info">Aucune donnée disponible</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Lieu</th>
                                        <th class="text-end">Nombre d'offres</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stats['offersByLocation'] as $location): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($location['location']) ?></td>
                                            <td class="text-end"><?= $location['offer_count'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Statistiques par compétences -->
        <div class="col-md-12 mt-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0"><i class="fas fa-code me-2"></i>Compétences les plus demandées</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($stats['popularSkills'])): ?>
                        <div class="alert alert-info">Aucune donnée disponible</div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($stats['popularSkills'] as $skill): ?>
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars($skill['name']) ?></h5>
                                            <p class="card-text">
                                                Catégorie: <span class="text-muted"><?= htmlspecialchars($skill['category']) ?></span>
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-primary"><?= $skill['skill_count'] ?> offres</span>
                                                <a href="index.php?page=offers&skills[]=<?= $skill['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                    Voir les offres
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../Templates/footer.php'; ?>
