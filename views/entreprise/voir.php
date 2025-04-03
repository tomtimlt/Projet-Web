<?php
$pageTitle = htmlspecialchars($entreprise->nom);
require_once 'views/Templates/header.php';
?>

<div class="company-details-container">
    <div class="company-header">
        <h1><?php echo htmlspecialchars($entreprise->nom); ?></h1>
        <div class="company-sector"><?php echo htmlspecialchars($entreprise->secteur_activite); ?></div>
    </div>
    
    <div class="company-grid">
        <div class="company-info">
            <div class="card">
                <div class="card-header">
                    <h3>Informations</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <strong>Adresse :</strong>
                        <p>
                            <?php echo htmlspecialchars($entreprise->adresse); ?><br>
                            <?php echo htmlspecialchars($entreprise->code_postal . ' ' . $entreprise->ville); ?><br>
                            <?php echo htmlspecialchars($entreprise->pays); ?>
                        </p>
                    </div>
                    
                    <div class="info-item">
                        <strong>Contact :</strong>
                        <p>
                            Email : <a href="mailto:<?php echo htmlspecialchars($entreprise->email_contact); ?>"><?php echo htmlspecialchars($entreprise->email_contact); ?></a><br>
                            Téléphone : <?php echo htmlspecialchars($entreprise->telephone_contact); ?>
                        </p>
                    </div>
                    
                    <?php if (!empty($entreprise->site_web)): ?>
                    <div class="info-item">
                        <strong>Site Web :</strong>
                        <p><a href="<?php echo htmlspecialchars($entreprise->site_web); ?>" target="_blank"><?php echo htmlspecialchars($entreprise->site_web); ?></a></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Description</h3>
                </div>
                <div class="card-body">
                    <p><?php echo nl2br(htmlspecialchars($entreprise->description)); ?></p>
                </div>
            </div>
            
            <?php if (isset($_SESSION['user_id']) && $this->verifierPermission('modifier')): ?>
                <div class="company-actions">
                    <a href="index.php?controller=entreprise&action=modifier&id=<?php echo $entreprise->id; ?>" class="btn btn-primary">Modifier</a>
                    <a href="index.php?controller=entreprise&action=supprimer&id=<?php echo $entreprise->id; ?>" class="btn btn-danger">Supprimer</a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="company-sidebar">
            <div class="card">
                <div class="card-header">
                    <h3>Évaluations</h3>
                </div>
                <div class="card-body">
                    <div class="rating-summary">
                        <div class="average-rating">
                            <span class="rating-value"><?php echo number_format($rating['average'], 1); ?></span>
                            <span class="rating-max">/5</span>
                        </div>
                        <div class="rating-stars">
                            <?php 
                            $fullStars = floor($rating['average']);
                            $halfStar = $rating['average'] - $fullStars >= 0.5;
                            
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $fullStars) {
                                    echo '<span class="star filled">★</span>';
                                } elseif ($i == $fullStars + 1 && $halfStar) {
                                    echo '<span class="star half-filled">★</span>';
                                } else {
                                    echo '<span class="star">★</span>';
                                }
                            }
                            ?>
                        </div>
                        <div class="rating-count">
                            <span><?php echo $rating['count']; ?> évaluation<?php echo $rating['count'] > 1 ? 's' : ''; ?></span>
                        </div>
                    </div>
                    
                    <?php if (count($evaluations) > 0): ?>
                        <div class="recent-evaluations">
                            <h4>Avis récents</h4>
                            <?php foreach ($evaluations as $evaluation): ?>
                                <div class="evaluation-item-preview">
                                    <div class="evaluation-stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="star <?php echo $i <= $evaluation->note ? 'filled' : ''; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <div class="evaluation-excerpt">
                                        <?php 
                                        $excerpt = htmlspecialchars($evaluation->commentaire);
                                        if (strlen($excerpt) > 100) {
                                            $excerpt = substr($excerpt, 0, 100) . '...';
                                        }
                                        echo $excerpt; 
                                        ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php if ($totalEvaluations > count($evaluations)): ?>
                                <div class="more-evaluations">
                                    <a href="index.php?controller=evaluation&action=parEntreprise&id=<?php echo $entreprise->id; ?>" class="btn btn-secondary btn-sm">
                                        Voir toutes les évaluations (<?php echo $totalEvaluations; ?>)
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-evaluations">
                            <p>Aucune évaluation disponible pour cette entreprise.</p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (!$evaluation_utilisateur): ?>
                            <div class="add-evaluation">
                                <a href="index.php?controller=evaluation&action=parEntreprise&id=<?php echo $entreprise->id; ?>" class="btn btn-primary btn-block">
                                    Évaluer cette entreprise
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="add-evaluation">
                                <a href="index.php?controller=evaluation&action=parEntreprise&id=<?php echo $entreprise->id; ?>" class="btn btn-secondary btn-block">
                                    Modifier votre évaluation
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="add-evaluation">
                            <a href="index.php?controller=auth&action=login" class="btn btn-primary btn-block">
                                Connectez-vous pour évaluer
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Statistiques</h3>
                </div>
                <div class="card-body">
                    <ul class="stats-list">
                        <?php foreach ($statistiques as $key => $value): ?>
                            <li>
                                <span class="stat-label"><?php echo htmlspecialchars($key); ?></span>
                                <span class="stat-value"><?php echo htmlspecialchars($value); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .company-details-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .company-header {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .company-header h1 {
        margin-bottom: 5px;
        color: #2c3e50;
    }
    
    .company-sector {
        color: #7f8c8d;
        font-size: 18px;
    }
    
    .company-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }
    
    .card {
        background-color: white;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        overflow: hidden;
    }
    
    .card-header {
        background-color: #f8f9fa;
        padding: 15px 20px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .card-header h3 {
        margin: 0;
        color: #2c3e50;
    }
    
    .card-body {
        padding: 20px;
    }
    
    .info-item {
        margin-bottom: 15px;
    }
    
    .info-item strong {
        color: #2c3e50;
        display: block;
        margin-bottom: 5px;
    }
    
    .info-item p {
        margin: 0;
        line-height: 1.5;
    }
    
    .company-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    
    .btn {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: bold;
        text-align: center;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    
    .btn-block {
        display: block;
        width: 100%;
    }
    
    .btn-sm {
        padding: 5px 10px;
        font-size: 14px;
    }
    
    .btn-primary {
        background-color: #4CAF50;
        color: white;
    }
    
    .btn-primary:hover {
        background-color: #3e8e41;
    }
    
    .btn-secondary {
        background-color: #f1f1f1;
        color: #333;
    }
    
    .btn-secondary:hover {
        background-color: #ddd;
    }
    
    .btn-danger {
        background-color: #f44336;
        color: white;
    }
    
    .btn-danger:hover {
        background-color: #d32f2f;
    }
    
    .rating-summary {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .average-rating {
        font-size: 24px;
        font-weight: bold;
    }
    
    .rating-value {
        color: #ff9800;
    }
    
    .rating-max {
        color: #777;
    }
    
    .rating-stars {
        margin: 10px 0;
    }
    
    .star {
        font-size: 24px;
        color: #ddd;
        margin: 0 2px;
    }
    
    .star.filled {
        color: #ff9800;
    }
    
    .star.half-filled {
        color: #ff9800;
        position: relative;
    }
    
    .star.half-filled::after {
        content: "★";
        position: absolute;
        left: 0;
        top: 0;
        width: 50%;
        overflow: hidden;
        color: #ddd;
    }
    
    .rating-count {
        color: #777;
    }
    
    .recent-evaluations {
        margin-top: 20px;
    }
    
    .recent-evaluations h4 {
        margin-bottom: 15px;
        color: #2c3e50;
    }
    
    .evaluation-item-preview {
        border-top: 1px solid #eee;
        padding: 15px 0;
    }
    
    .evaluation-item-preview:first-child {
        border-top: none;
    }
    
    .evaluation-stars {
        margin-bottom: 10px;
    }
    
    .evaluation-stars .star {
        font-size: 16px;
    }
    
    .evaluation-excerpt {
        color: #555;
        font-size: 14px;
        line-height: 1.5;
    }
    
    .more-evaluations {
        margin-top: 15px;
        text-align: center;
    }
    
    .add-evaluation {
        margin-top: 20px;
    }
    
    .no-evaluations {
        text-align: center;
        color: #777;
        padding: 20px 0;
    }
    
    .stats-list {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }
    
    .stats-list li {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }
    
    .stats-list li:last-child {
        border-bottom: none;
    }
    
    .stat-label {
        color: #555;
    }
    
    .stat-value {
        font-weight: bold;
        color: #2c3e50;
    }
    
    @media (max-width: 768px) {
        .company-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php require_once 'views/templates/footer.php'; ?>
