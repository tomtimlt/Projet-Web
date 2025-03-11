<?php include_once __DIR__ . '/../Templates/header.php'; ?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?page=offers">Offres de stage</a></li>
            <li class="breadcrumb-item active" aria-current="page">Statistiques</li>
        </ol>
    </nav>

    <h1 class="mb-4">
        <i class="fas fa-chart-bar me-2"></i>Statistiques des offres de stage
    </h1>

    <div class="row">
        <!-- Cartes d'information -->
        <div class="col-md-12 mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card h-100 border-primary">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                    <i class="fas fa-file-alt text-primary fa-2x"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0"><?= $statistics['total_offers'] ?></h3>
                                    <p class="text-muted mb-0">Offres de stage</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php 
                $activeOffers = 0;
                $inactiveOffers = 0;
                $filledOffers = 0;
                
                foreach ($statistics['offers_by_status'] as $statusData) {
                    switch ($statusData['status']) {
                        case 'active':
                            $activeOffers = $statusData['count'];
                            break;
                        case 'inactive':
                            $inactiveOffers = $statusData['count'];
                            break;
                        case 'filled':
                            $filledOffers = $statusData['count'];
                            break;
                    }
                }
                ?>
                
                <div class="col-md-4">
                    <div class="card h-100 border-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                                    <i class="fas fa-check-circle text-success fa-2x"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0"><?= $activeOffers ?></h3>
                                    <p class="text-muted mb-0">Offres actives</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 border-info">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                                    <i class="fas fa-user-check text-info fa-2x"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0"><?= $filledOffers ?></h3>
                                    <p class="text-muted mb-0">Offres pourvues</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Offres les plus populaires -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i>Offres les plus populaires</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($statistics['most_popular_offers'])): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Aucune donnée disponible
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Offre</th>
                                        <th>Entreprise</th>
                                        <th>Candidatures</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($statistics['most_popular_offers'] as $offer): ?>
                                        <tr>
                                            <td>
                                                <a href="index.php?page=offers&action=view&id=<?= $offer['id'] ?>" class="text-decoration-none">
                                                    <?= htmlspecialchars($offer['title']) ?>
                                                </a>
                                            </td>
                                            <td><?= htmlspecialchars($offer['company_name']) ?></td>
                                            <td>
                                                <span class="badge bg-primary"><?= $offer['application_count'] ?></span>
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
        
        <!-- Répartition par entreprise -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>Offres par entreprise</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($statistics['companies_distribution'])): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Aucune donnée disponible
                        </div>
                    <?php else: ?>
                        <canvas id="companiesChart" width="400" height="300"></canvas>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Répartition des statuts -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-pie-chart me-2"></i>Répartition par statut</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($statistics['offers_by_status'])): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Aucune donnée disponible
                        </div>
                    <?php else: ?>
                        <canvas id="statusChart" width="400" height="300"></canvas>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Répartition des compétences -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-code-branch me-2"></i>Compétences les plus demandées</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($statistics['skills_distribution'])): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Aucune donnée disponible
                        </div>
                    <?php else: ?>
                        <canvas id="skillsChart" width="400" height="300"></canvas>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Répartition par entreprise
    <?php if (!empty($statistics['companies_distribution'])): ?>
    const companiesData = {
        labels: [
            <?php foreach ($statistics['companies_distribution'] as $company): ?>
                '<?= addslashes($company['name']) ?>',
            <?php endforeach; ?>
        ],
        datasets: [{
            data: [
                <?php foreach ($statistics['companies_distribution'] as $company): ?>
                    <?= $company['offer_count'] ?>,
                <?php endforeach; ?>
            ],
            backgroundColor: [
                '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                '#5a5c69', '#858796', '#f8f9fc', '#d1d3e2', '#6610f2'
            ],
            hoverBackgroundColor: [
                '#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617',
                '#3a3b45', '#60616f', '#d5d8e2', '#a8aac7', '#4e0cb0'
            ],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }]
    };
    
    const companiesCtx = document.getElementById('companiesChart');
    new Chart(companiesCtx, {
        type: 'bar',
        data: companiesData,
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10,
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' offre(s)';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
    <?php endif; ?>
    
    // Répartition par statut
    <?php if (!empty($statistics['offers_by_status'])): ?>
    const statusData = {
        labels: [
            <?php foreach ($statistics['offers_by_status'] as $status): ?>
                '<?= $status['status'] == 'active' ? 'Actif' : ($status['status'] == 'inactive' ? 'Inactif' : 'Pourvu') ?>',
            <?php endforeach; ?>
        ],
        datasets: [{
            data: [
                <?php foreach ($statistics['offers_by_status'] as $status): ?>
                    <?= $status['count'] ?>,
                <?php endforeach; ?>
            ],
            backgroundColor: ['#1cc88a', '#f6c23e', '#36b9cc'],
            hoverBackgroundColor: ['#17a673', '#dda20a', '#2c9faf'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }]
    };
    
    const statusCtx = document.getElementById('statusChart');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: statusData,
        options: {
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                            const percentage = Math.round((context.parsed * 100) / total) + '%';
                            return context.parsed + ' offre(s) (' + percentage + ')';
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });
    <?php endif; ?>
    
    // Répartition par compétence
    <?php if (!empty($statistics['skills_distribution'])): ?>
    const skillsData = {
        labels: [
            <?php 
            // Limiter aux 10 compétences les plus demandées
            $count = 0;
            foreach ($statistics['skills_distribution'] as $skill) {
                if ($count++ < 10) {
                    echo "'" . addslashes($skill['name']) . "',";
                }
            }
            ?>
        ],
        datasets: [{
            label: 'Nombre d\'offres',
            data: [
                <?php 
                $count = 0;
                foreach ($statistics['skills_distribution'] as $skill) {
                    if ($count++ < 10) {
                        echo $skill['offer_count'] . ',';
                    }
                }
                ?>
            ],
            backgroundColor: 'rgba(78, 115, 223, 0.7)',
            borderColor: 'rgba(78, 115, 223, 1)',
            borderWidth: 1
        }]
    };
    
    const skillsCtx = document.getElementById('skillsChart');
    new Chart(skillsCtx, {
        type: 'horizontalBar',
        type: 'bar',
        data: skillsData,
        options: {
            indexAxis: 'y',
            maintainAspectRatio: false,
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
    <?php endif; ?>
});
</script>

<?php include_once __DIR__ . '/../Templates/footer.php'; ?>
