<?php
// S'assurer que l'utilisateur est connecté pour pouvoir évaluer
$userLoggedIn = isset($_SESSION['user_id']);

// Vérifier si l'utilisateur a déjà évalué cette entreprise
$hasEvaluated = false;
if ($userLoggedIn && isset($this->userEvaluation)) {
    $hasEvaluated = true;
}
?>

<div class="evaluation-form">
    <h4><?php echo $hasEvaluated ? 'Modifier votre évaluation' : 'Évaluer cette entreprise'; ?></h4>
    
    <?php if (!$userLoggedIn): ?>
        <p>Vous devez être <a href="index.php?action=login">connecté</a> pour évaluer cette entreprise.</p>
    <?php else: ?>
        <form action="index.php?action=store_evaluation&entreprise_id=<?php echo $this->entrepriseModel->id; ?>" method="post" id="evaluationForm">
            <?php if ($hasEvaluated): ?>
                <input type="hidden" name="evaluation_id" value="<?php echo htmlspecialchars($this->userEvaluation->id); ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="note">Note</label>
                <div class="rating-selector">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <input type="radio" name="note" id="star<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo ($hasEvaluated && $this->userEvaluation->note == $i) ? 'checked' : ''; ?> required>
                        <label for="star<?php echo $i; ?>" class="star-label">★</label>
                    <?php endfor; ?>
                </div>
                <div class="error-message" id="note-error"></div>
            </div>
            
            <div class="form-group">
                <label for="commentaire">Commentaire</label>
                <textarea id="commentaire" name="commentaire" rows="4" required><?php echo $hasEvaluated ? htmlspecialchars($this->userEvaluation->commentaire) : ''; ?></textarea>
                <div class="error-message" id="commentaire-error"></div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit"><?php echo $hasEvaluated ? 'Mettre à jour' : 'Soumettre'; ?></button>
            </div>
        </form>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('evaluationForm');
                
                if (form) {
                    form.addEventListener('submit', function(event) {
                        // Réinitialise les messages d'erreur
                        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
                        
                        // Vérifie si une note a été sélectionnée
                        const noteSelected = form.querySelector('input[name="note"]:checked');
                        if (!noteSelected) {
                            document.getElementById('note-error').textContent = 'Veuillez attribuer une note';
                            event.preventDefault();
                        }
                        
                        // Vérifie si un commentaire a été saisi
                        const commentaire = document.getElementById('commentaire').value.trim();
                        if (!commentaire) {
                            document.getElementById('commentaire-error').textContent = 'Veuillez laisser un commentaire';
                            event.preventDefault();
                        }
                    });
                }
            });
        </script>
    <?php endif; ?>
</div>

<style>
    .rating-selector {
        display: inline-flex;
        flex-direction: row-reverse;
        margin-top: 10px;
    }
    
    .rating-selector input {
        display: none;
    }
    
    .rating-selector label {
        font-size: 24px;
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
</style>