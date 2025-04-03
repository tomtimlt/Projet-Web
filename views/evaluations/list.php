<?php
$pageTitle = 'Évaluations de ' . htmlspecialchars($entreprise->name);
require_once 'views/Templates/header.php';
?>

<div class="evaluations-container">
    <div class="section-header">
        <h2>Évaluations de <?php echo htmlspecialchars($entreprise->name); ?></h2>
        <div class="company-rating">
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
        </div>
    </div>
    
    <?php if (isset($_SESSION['user_id']) && !$evaluation_utilisateur): ?>
    <div class="evaluation-form-container">
        <h3>Donnez votre avis sur cette entreprise</h3>
        <form action="index.php?page=companies&action=rate" method="post" class="evaluation-form">
            <input type="hidden" name="entreprise_id" value="<?php echo $entreprise->id; ?>">
            
            <div class="form-group rating-input">
                <label>Votre note :</label>
                <div class="rating-selector">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <input type="radio" name="note" id="note<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo $i == 5 ? 'checked' : ''; ?>>
                    <label for="note<?php echo $i; ?>" class="star-label">★</label>
                    <?php endfor; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="commentaire">Votre commentaire :</label>
                <textarea name="commentaire" id="commentaire" rows="4" required></textarea>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Soumettre votre évaluation</button>
            </div>
        </form>
    </div>
    <?php elseif (isset($_SESSION['user_id']) && $evaluation_utilisateur): ?>
    <div class="user-evaluation">
        <h3>Votre évaluation</h3>
        <div class="evaluation-item user-evaluation-item">
            <div class="evaluation-header">
                <div class="rating-stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star <?php echo $i <= $evaluation_utilisateur['note'] ? 'filled' : ''; ?>">★</span>
                    <?php endfor; ?>
                </div>
                <div class="evaluation-date">
                    <span>Évalué le <?php echo date('d/m/Y', strtotime($evaluation_utilisateur['date_evaluation'])); ?></span>
                </div>
            </div>
            <div class="evaluation-content">
                <p><?php echo nl2br(htmlspecialchars($evaluation_utilisateur['commentaire'])); ?></p>
            </div>
            <div class="evaluation-actions">
                <button id="btn-edit-evaluation" class="btn btn-secondary">Modifier</button>
                <a href="index.php?page=company_delete_rating&id=<?php echo $evaluation_utilisateur['id']; ?>" 
                   class="btn btn-danger" 
                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre évaluation ?');">Supprimer</a>
            </div>
        </div>
        
        <!-- Formulaire de modification (caché par défaut) -->
        <div id="edit-evaluation-form" class="evaluation-form-container" style="display: none;">
            <h3>Modifier votre évaluation</h3>
            <form action="index.php?page=companies&action=rate" method="post" class="evaluation-form">
                <input type="hidden" name="entreprise_id" value="<?php echo $entreprise->id; ?>">
                
                <div class="form-group rating-input">
                    <label>Votre note :</label>
                    <div class="rating-selector">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <input type="radio" name="note" id="edit_note<?php echo $i; ?>" value="<?php echo $i; ?>" 
                               <?php echo $i == $evaluation_utilisateur['note'] ? 'checked' : ''; ?>>
                        <label for="edit_note<?php echo $i; ?>" class="star-label">★</label>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_commentaire">Votre commentaire :</label>
                    <textarea name="commentaire" id="edit_commentaire" rows="4" required><?php echo htmlspecialchars($evaluation_utilisateur['commentaire']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    <button type="button" id="btn-cancel-edit" class="btn btn-secondary">Annuler</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="evaluations-list">
        <h3>Avis des utilisateurs</h3>
        
        <?php if (count($evaluations) > 0): ?>
            <?php foreach ($evaluations as $evaluation): ?>
                <?php 
                // Ne pas afficher à nouveau l'évaluation de l'utilisateur courant dans la liste
                if (isset($_SESSION['user_id']) && $evaluation->user_id == $_SESSION['user_id']) continue; 
                ?>
                <div class="evaluation-item">
                    <div class="evaluation-header">
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($evaluation->user_firstname . ' ' . $evaluation->user_lastname); ?></span>
                            <span class="user-role">(<?php echo htmlspecialchars($evaluation->user_role); ?>)</span>
                        </div>
                        <div class="evaluation-date">
                            <span>Évalué le <?php echo date('d/m/Y', strtotime($evaluation->created_at)); ?></span>
                        </div>
                    </div>
                    <div class="rating-stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?php echo $i <= $evaluation->rating ? 'filled' : ''; ?>">★</span>
                        <?php endfor; ?>
                    </div>
                    <div class="evaluation-content">
                        <p><?php echo nl2br(htmlspecialchars($evaluation->comment)); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="index.php?page=companies&action=view&id=<?php echo $entreprise->id; ?>&page_eval=<?php echo $i; ?>" 
                       class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="no-evaluations">
                <p>Aucune évaluation disponible pour cette entreprise.</p>
                <?php if (isset($_SESSION['user_id']) && !$evaluation_utilisateur): ?>
                    <p>Soyez le premier à donner votre avis !</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="back-link">
        <a href="index.php?page=companies&action=view&id=<?php echo $entreprise->id; ?>" class="btn btn-secondary">
            Retour à la page de l'entreprise
        </a>
    </div>
</div>

<style>
    .evaluations-container {
        max-width: 960px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .section-header {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .company-rating {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
    
    .rating-summary {
        display: flex;
        flex-direction: column;
        align-items: center;
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
    
    .evaluation-form-container {
        background-color: #f9f9f9;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 30px;
    }
    
    .evaluation-form-container h3 {
        margin-top: 0;
        margin-bottom: 20px;
        color: #333;
    }
    
    .evaluation-form .form-group {
        margin-bottom: 20px;
    }
    
    .evaluation-form label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
    }
    
    .rating-input {
        display: flex;
        align-items: center;
    }
    
    .rating-selector {
        display: inline-flex;
        flex-direction: row-reverse;
        margin-left: 15px;
    }
    
    .rating-selector input {
        display: none;
    }
    
    .rating-selector label {
        font-size: 30px;
        color: #ddd;
        cursor: pointer;
        margin: 0 2px;
        transition: color 0.2s;
    }
    
    .rating-selector label:hover,
    .rating-selector label:hover ~ label,
    .rating-selector input:checked ~ label {
        color: #ff9800;
    }
    
    .evaluation-form textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    
    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s;
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
    
    .user-evaluation {
        margin-bottom: 30px;
    }
    
    .user-evaluation-item {
        border: 2px solid #4CAF50;
    }
    
    .evaluation-item {
        background-color: white;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .evaluation-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .user-info {
        font-weight: bold;
    }
    
    .user-role {
        color: #777;
        font-weight: normal;
    }
    
    .evaluation-date {
        color: #777;
        font-size: 14px;
    }
    
    .evaluation-content {
        margin: 15px 0;
        line-height: 1.6;
    }
    
    .evaluation-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }
    
    .evaluations-list h3 {
        margin-bottom: 20px;
    }
    
    .no-evaluations {
        background-color: #f9f9f9;
        padding: 30px;
        text-align: center;
        border-radius: 5px;
    }
    
    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 30px;
    }
    
    .page-link {
        display: inline-block;
        padding: 8px 12px;
        margin: 0 5px;
        background-color: #f1f1f1;
        color: #333;
        text-decoration: none;
        border-radius: 3px;
    }
    
    .page-link.active {
        background-color: #4CAF50;
        color: white;
    }
    
    .back-link {
        margin-top: 30px;
        text-align: center;
    }
    
    @media (max-width: 768px) {
        .evaluation-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .evaluation-date {
            margin-top: 5px;
        }
        
        .rating-input {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .rating-selector {
            margin-left: 0;
            margin-top: 10px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du bouton Modifier pour l'évaluation de l'utilisateur
    const btnEditEvaluation = document.getElementById('btn-edit-evaluation');
    const userEvaluationItem = document.querySelector('.user-evaluation-item');
    const editEvaluationForm = document.getElementById('edit-evaluation-form');
    const btnCancelEdit = document.getElementById('btn-cancel-edit');
    
    if (btnEditEvaluation) {
        btnEditEvaluation.addEventListener('click', function() {
            userEvaluationItem.style.display = 'none';
            editEvaluationForm.style.display = 'block';
        });
    }
    
    if (btnCancelEdit) {
        btnCancelEdit.addEventListener('click', function() {
            editEvaluationForm.style.display = 'none';
            userEvaluationItem.style.display = 'block';
        });
    }
});
</script>

<?php require_once 'views/templates/footer.php'; ?>