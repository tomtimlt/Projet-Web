<?php
$pageTitle = 'Statistiques Globales';
require_once 'views/templates/header.php';
?>

<div class="statistics-page">
    <div class="section-header">
        <h2>Statistiques Globales</h2>
        <p>Aperçu et analyses des entreprises et évaluations dans le système</p>
    </div>
    
    <div class="stats-cards">
        <div class="stat-card total-companies">
            <div class="stat-icon">🏢</div>
            <div class="stat-info">
                <div class="stat-value"><?php echo isset($this->stats['total_entreprises']) ? $this->stats['total_entreprises'] : 0; ?></div>
                <div class="stat-label">Entreprises enregistrées</div>
            </div>
        </div>
        
        <div class="stat-card total-evaluations">
            <div class="stat-icon">⭐</div>
            <div class="stat-info">
                <div class="stat-value"><?php echo isset($this->stats['total_evaluations']) ? $this->stats['total_evaluations'] : 0; ?></div>
                <div class="stat-label">Évaluations</div>
            </div>
        </div>
        
        <div class="stat-card average-rating">
            <div class="stat-icon">📊</div>
            <div class="stat-info">
                <div class="stat-value"><?php echo isset($this->stats['note_moyenne_generale']) ? number_format($this->stats['note_moyenne_generale'], 1) : 0; ?></div>
                <div class="stat-label">Note moyenne globale</div>
            </div>
        </div>
        
        <div class="stat-card total-sectors">
            <div class="stat-icon">🔍</div>
            <div class="stat-info">
                <div class="stat-value"><?php echo isset($this->stats['nb_secteurs']) ? $this->stats['nb_secteurs'] : 0; ?></div>
                <div class="stat-label">Secteurs d'activité</div>
            </div>
        </div>
    </div>
    
    <?php if (isset($this->stats['meilleures_entreprises']) && count($this->stats['meilleures_entreprises']) > 0): ?>
    <div class="stats-section">
        <h3>Meilleures entreprises</h3>
        <div class="top-companies">
            <?php foreach ($this->stats['meilleures_entreprises'] as $index => $entreprise): ?>
            <div class="top-company-card">
                <div class="rank">#<?php echo $index + 1; ?></div>
                <div class="company-info">
                    <h4><a href="index.php?action=view_entreprise&id=<?php echo $entreprise->id; ?>"><?php echo htmlspecialchars($entreprise->nom); ?></a></h4>
                    <div class="company-meta">
                        <span class="sector"><?php echo htmlspecialchars($entreprise->secteur_activite); ?></span>
                        <span class="location"><?php echo htmlspecialchars($entreprise->ville); ?>, <?php echo htmlspecialchars($entreprise->pays); ?></span>
                    </div>
                </div>
                <div class="rating">
                    <div class="stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?php echo $i <= round($entreprise->note_moyenne) ? 'filled' : ''; ?>">★</span>
                        <?php endfor; ?>
                    </div>
                    <span class="average">(<?php echo number_format($entreprise->note_moyenne, 1); ?>)</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (isset($this->stats['secteurs_populaires']) && count($this->stats['secteurs_populaires']) > 0): ?>
    <div class="stats-section">
        <h3>Secteurs d'activité populaires</h3>
        <div class="sectors-chart">
            <div class="chart-bars">
                <?php foreach ($this->stats['secteurs_populaires'] as $secteur): ?>
                <?php $pourcentage = ($secteur->count / $this->stats['total_entreprises']) * 100; ?>
                <div class="chart-item">
                    <div class="chart-label"><?php echo htmlspecialchars($secteur->secteur_activite); ?></div>
                    <div class="chart-bar-container">
                        <div class="chart-bar" style="width: <?php echo min(100, $pourcentage); ?>%;"></div>
                        <div class="chart-value"><?php echo $secteur->count; ?> (<?php echo round($pourcentage); ?>%)</div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (isset($this->stats['villes_populaires']) && count($this->stats['villes_populaires']) > 0): ?>
    <div class="stats-section">
        <h3>Villes les plus représentées</h3>
        <div class="cities-distribution">
            <?php foreach ($this->stats['villes_populaires'] as $ville): ?>
            <?php $pourcentage = ($ville->count / $this->stats['total_entreprises']) * 100; ?>
            <div class="city-item">
                <div class="city-name"><?php echo htmlspecialchars($ville->ville); ?></div>
                <div class="city-count"><?php echo $ville->count; ?></div>
                <div class="city-bar-container">
                    <div class="city-bar" style="width: <?php echo min(100, $pourcentage); ?>%;"></div>
                </div>
                <div class="city-percentage"><?php echo round($pourcentage); ?>%</div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (isset($this->stats['recentes_evaluations']) && count($this->stats['recentes_evaluations']) > 0): ?>
    <div class="stats-section">
        <h3>Évaluations récentes</h3>
        <div class="recent-evaluations">
            <?php foreach ($this->stats['recentes_evaluations'] as $evaluation): ?>
            <div class="evaluation-card">
                <div class="evaluation-header">
                    <h4>
                        <a href="index.php?action=view_entreprise&id=<?php echo $evaluation->entreprise_id; ?>">
                            <?php echo htmlspecialchars($evaluation->entreprise_nom); ?>
                        </a>
                    </h4>
                    <div class="evaluation-meta">
                        <div class="stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="star <?php echo $i <= $evaluation->note ? 'filled' : ''; ?>">★</span>
                            <?php endfor; ?>
                        </div>
                        <span class="evaluation-date"><?php echo date('d/m/Y', strtotime($evaluation->date_evaluation)); ?></span>
                    </div>
                </div>
                <div class="user-info">
                    <span class="user-name"><?php echo htmlspecialchars($evaluation->prenom . ' ' . $evaluation->nom); ?></span>
                </div>
                <div class="evaluation-content">
                    <p><?php echo nl2br(htmlspecialchars($evaluation->commentaire)); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
    .statistics-page {
        margin-bottom: 40px;
    }
    
    .section-header {
        margin-bottom: 30px;
        text-align: center;
    }
    
    .section-header h2 {
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    .section-header p {
        color: #7f8c8d;
    }
    
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }
    
    .stat-card {
        background-color: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        transition: transform 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-icon {
        font-size: 36px;
        margin-right: 15px;
        color: #3498db;
    }
    
    .stat-info {
        flex: 1;
    }
    
    .stat-value {
        font-size: 28px;
        font-weight: bold;
        color: #2c3e50;
        line-height: 1.2;
    }
    
    .stat-label {
        font-size: 14px;
        color: #7f8c8d;
    }
    
    .stats-section {
        background-color: white;
        border-radius: 8px;
        padding: 25px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }
    
    .stats-section h3 {
        color: #2c3e50;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .top-companies {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 15px;
    }
    
    .top-company-card {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        transition: background-color 0.3s;
    }
    
    .top-company-card:hover {
        background-color: #f1f1f1;
    }
    
    .rank {
        font-size: 24px;
        font-weight: bold;
        color: #3498db;
        min-width: 40px;
        text-align: center;
    }
    
    .company-info {
        flex: 1;
        min-width: 150px;
    }
    
    .company-info h4 {
        margin-bottom: 5px;
        font-size: 16px;
    }
    
    .company-info h4 a {
        color: #2c3e50;
        text-decoration: none;
    }
    
    .company-info h4 a:hover {
        color: #3498db;
    }
    
    .company-meta {
        display: flex;
        flex-direction: column;
        font-size: 12px;
        color: #7f8c8d;
    }
    
    .rating {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }
    
    .stars {
        display: inline-flex;
    }
    
    .star {
        font-size: 16px;
        color: #ddd;
    }
    
    .star.filled {
        color: #f39c12;
    }
    
    .average {
        font-size: 12px;
        color: #7f8c8d;
    }
    
    .chart-bars {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .chart-item {
        display: flex;
        align-items: center;
    }
    
    .chart-label {
        width: 25%;
        min-width: 100px;
        font-size: 14px;
        padding-right: 15px;
    }
    
    .chart-bar-container {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .chart-bar {
        height: 20px;
        background-color: #3498db;
        border-radius: 4px;
        transition: width 0.5s ease-in-out;
    }
    
    .chart-value {
        font-size: 14px;
        color: #7f8c8d;
    }
    
    .cities-distribution {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .city-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .city-name {
        width: 20%;
        min-width: 100px;
        font-size: 14px;
    }
    
    .city-count {
        width: 50px;
        font-size: 14px;
        font-weight: bold;
        text-align: right;
    }
    
    .city-bar-container {
        flex: 1;
        background-color: #f1f1f1;
        border-radius: 4px;
        height: 15px;
        overflow: hidden;
    }
    
    .city-bar {
        height: 100%;
        background-color: #2ecc71;
        transition: width 0.5s ease-in-out;
    }
    
    .city-percentage {
        width: 50px;
        font-size: 14px;
        color: #7f8c8d;
        text-align: right;
    }
    
    .recent-evaluations {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }
    
    .evaluation-card {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
    }
    
    .evaluation-header {
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .evaluation-header h4 {
        margin: 0;
        font-size: 16px;
    }
    
    .evaluation-header h4 a {
        color: #2c3e50;
        text-decoration: none;
    }
    
    .evaluation-header h4 a:hover {
        color: #3498db;
    }
    
    .evaluation-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 5px;
    }
    
    .evaluation-date {
        font-size: 12px;
        color: #7f8c8d;
    }
    
    .user-info {
        margin-bottom: 10px;
        font-size: 14px;
        color: #7f8c8d;
    }
    
    .evaluation-content {
        font-size: 14px;
        line-height: 1.5;
        color: #555;
    }
    
    @media (max-width: 768px) {
        .stats-cards {
            grid-template-columns: 1fr;
        }
        
        .top-companies,
        .recent-evaluations {
            grid-template-columns: 1fr;
        }
        
        .chart-item,
        .city-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }
        
        .chart-label,
        .city-name {
            width: 100%;
            margin-bottom: 5px;
        }
        
        .chart-bar-container,
        .city-bar-container {
            width: 100%;
        }
        
        .city-count,
        .city-percentage {
            text-align: left;
        }
        
        .evaluation-header {
            flex-direction: column;
        }
        
        .evaluation-meta {
            align-items: flex-start;
        }
    }
</style>

<?php require_once 'views/templates/footer.php'; ?>
