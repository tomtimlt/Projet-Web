<?php
$pageTitle = "Statistiques des étudiants";
require_once __DIR__ . '/../Templates/header.php';
$auth = \Models\Auth::getInstance();
?>

<div class="container mt-4">
    <h1 class="mb-4">Statistiques des recherches de stages</h1>
    
    <!-- Affichage des messages flash -->
    <?php require_once 'Views/Templates/flash.php'; ?>
    
    <div class="row">
        <!-- Statistiques générales -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h2 class="h5 mb-0">Vue d'ensemble</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="text-center mb-4">
                                <h3 class="display-4"><?= $globalStats['student_count'] ?></h3>
                                <p class="text-muted">Nombre total d'étudiants</p>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col">
                                    <div class="card bg-light text-center">
                                        <div class="card-body py-3">
                                            <h4 class="h2 mb-0"><?= $globalStats['applications']['total'] ?></h4>
                                            <p class="text-muted mb-0">Candidatures totales</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card bg-light text-center">
                                        <div class="card-body py-3">
                                            <h4 class="h2 mb-0"><?= $globalStats['average_applications'] ?></h4>
                                            <p class="text-muted mb-0">Moy. par étudiant</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistiques des candidatures -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h2 class="h5 mb-0">Statut des candidatures</h2>
                </div>
                <div class="card-body">
                    <canvas id="applicationStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Tableau de bord mensuel -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h2 class="h5 mb-0">Évolution des candidatures</h2>
                </div>
                <div class="card-body">
                    <canvas id="monthlyActivityChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h2 class="h5 mb-0">Top 10 des compétences recherchées</h2>
                </div>
                <div class="card-body">
                    <!-- Simulation de données pour l'exemple -->
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="skillsChart"></canvas>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Compétence</th>
                                        <th>Occurrences</th>
                                        <th>% des offres</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>PHP</td>
                                        <td>24</td>
                                        <td>78%</td>
                                    </tr>
                                    <tr>
                                        <td>JavaScript</td>
                                        <td>22</td>
                                        <td>71%</td>
                                    </tr>
                                    <tr>
                                        <td>HTML/CSS</td>
                                        <td>20</td>
                                        <td>65%</td>
                                    </tr>
                                    <tr>
                                        <td>SQL</td>
                                        <td>18</td>
                                        <td>58%</td>
                                    </tr>
                                    <tr>
                                        <td>React</td>
                                        <td>15</td>
                                        <td>48%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-between mb-4">
        <a href="index.php?controller=student&action=index" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste des étudiants
        </a>
        
        <?php if ($auth->hasRole(['admin'])) : ?>
            <a href="index.php?controller=export&action=statistics" class="btn btn-primary">
                <i class="fas fa-file-export"></i> Exporter les statistiques
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Inclure Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Configuration des graphiques avec Chart.js
    document.addEventListener('DOMContentLoaded', function() {
        // Graphique des statuts de candidature
        const applicationStatusCtx = document.getElementById('applicationStatusChart').getContext('2d');
        const applicationStatusChart = new Chart(applicationStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['En attente', 'Acceptées', 'Refusées', 'Retirées'],
                datasets: [{
                    data: [
                        <?= $globalStats['applications']['pending'] ?>,
                        <?= $globalStats['applications']['accepted'] ?>,
                        <?= $globalStats['applications']['rejected'] ?>,
                        <?= $globalStats['applications']['withdrawn'] ?>
                    ],
                    backgroundColor: [
                        '#ffc107', // warning
                        '#28a745', // success
                        '#dc3545', // danger
                        '#6c757d'  // secondary
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        // Graphique de l'évolution mensuelle (données simulées)
        const monthlyActivityCtx = document.getElementById('monthlyActivityChart').getContext('2d');
        const monthlyActivityChart = new Chart(monthlyActivityCtx, {
            type: 'bar',
            data: {
                labels: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin'],
                datasets: [{
                    label: 'Candidatures envoyées',
                    data: [12, 19, 25, 31, 42, 15],
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }, {
                    label: 'Offres en wishlist',
                    data: [8, 15, 20, 25, 30, 10],
                    backgroundColor: 'rgba(255, 159, 64, 0.5)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Graphique des compétences les plus demandées (données simulées)
        const skillsCtx = document.getElementById('skillsChart').getContext('2d');
        const skillsChart = new Chart(skillsCtx, {
            type: 'horizontalBar',
            data: {
                labels: ['PHP', 'JavaScript', 'HTML/CSS', 'SQL', 'React', 'Node.js', 'Laravel', 'Python', 'Java', 'Git'],
                datasets: [{
                    label: 'Occurrences dans les offres',
                    data: [24, 22, 20, 18, 15, 12, 10, 8, 7, 6],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(199, 199, 199, 0.7)',
                        'rgba(83, 102, 255, 0.7)',
                        'rgba(255, 99, 71, 0.7)',
                        'rgba(60, 179, 113, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>

<?php require_once 'Views/Templates/footer.php'; ?>
