<?php
$pageTitle = 'Mes u00c9valuations';
require_once 'views/templates/header.php';
?>

<div class="my-evaluations">
    <div class="section-header">
        <h2>Mes u00c9valuations</h2>
        <p>Consultez et gu00e9rez vos u00e9valuations d'entreprises</p>
    </div>
    
    <?php if (isset($this->evaluations) && count($this->evaluations) > 0): ?>
        <div class="evaluations-list">
            <?php foreach ($this->evaluations as $evaluation): ?>
                <div class="evaluation-item">
                    <div class="company-info">
                        <h3>
                            <a href="index.php?action=view_entreprise&id=<?php echo $evaluation->entreprise_id; ?>">
                                <?php echo htmlspecialchars($evaluation->entreprise_nom); ?>
                            </a>
                        </h3>
                        <div class="company-meta">
                            <span class="sector"><?php echo htmlspecialchars($evaluation->secteur_activite); ?></span>
                            <span class="location"><?php echo htmlspecialchars($evaluation->ville); ?>, <?php echo htmlspecialchars($evaluation->pays); ?></span>
                        </div>
                    </div>
                    
                    <div class="evaluation-content">
                        <div class="rating">
                            <div class="stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star <?php echo $i <= $evaluation->note ? 'filled' : ''; ?>">u2605</span>
                                <?php endfor; ?>
                            </div>
                            <span class="date"><?php echo date('d/m/Y', strtotime($evaluation->date_evaluation)); ?></span>
                        </div>
                        
                        <div class="comment">
                            <p><?php echo nl2br(htmlspecialchars($evaluation->commentaire)); ?></p>
                        </div>
                        
                        <div class="actions">
                            <a href="index.php?action=view_entreprise&id=<?php echo $evaluation->entreprise_id; ?>" class="btn-view">Voir l'entreprise</a>
                            <a href="index.php?action=delete_evaluation&id=<?php echo $evaluation->id; ?>" class="btn-delete" 
                               onclick="return confirm('u00cates-vous su00fbr de vouloir supprimer cette u00e9valuation ?');">Supprimer</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-evaluations">
            <p>Vous n'avez pas encore u00e9valu00e9 d'entreprises.</p>
            <p><a href="index.php?action=entreprises" class="btn-view">Du00e9couvrir des entreprises u00e0 u00e9valuer</a></p>
        </div>
    <?php endif; ?>
</div>

<style>
    .section-header {
        margin-bottom: 30px;
        text-align: center;
    }
    
    .section-header h2 {
        color: #2c3e50;
    }
    
    .section-header p {
        color: #7f8c8d;
    }
    
    .evaluations-list {
        margin-bottom: 40px;
    }
    
    .evaluation-item {
        background-color: white;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        overflow: hidden;
        display: flex;
        flex-wrap: wrap;
    }
    
    .company-info {
        padding: 20px;
        background-color: #f8f9fa;
        width: 30%;
        min-width: 250px;
    }
    
    .company-info h3 {
        margin-bottom: 10px;
    }
    
    .company-info h3 a {
        color: #2c3e50;
        text-decoration: none;
    }
    
    .company-info h3 a:hover {
        color: #3498db;
    }
    
    .company-meta {
        display: flex;
        flex-direction: column;
        gap: 5px;
        color: #7f8c8d;
        font-size: 14px;
    }
    
    .evaluation-content {
        padding: 20px;
        flex: 1;
        min-width: 250px;
    }
    
    .rating {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .stars .star {
        font-size: 18px;
        color: #ddd;
    }
    
    .stars .star.filled {
        color: #f39c12;
    }
    
    .date {
        color: #7f8c8d;
        font-size: 14px;
    }
    
    .comment {
        margin-bottom: 20px;
        line-height: 1.6;
    }
    
    .actions {
        display: flex;
        gap: 10px;
    }
    
    .no-evaluations {
        background-color: white;
        padding: 30px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }
    
    .no-evaluations p {
        margin-bottom: 20px;
    }
    
    @media (max-width: 768px) {
        .evaluation-item {
            flex-direction: column;
        }
        
        .company-info {
            width: 100%;
        }
    }
</style>

<?php require_once 'views/templates/footer.php'; ?>
