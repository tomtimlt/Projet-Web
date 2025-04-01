<?php
// S'assurer que l'utilisateur est connectu00e9 pour pouvoir u00e9valuer
$userLoggedIn = isset($_SESSION['user_id']);

// Vu00e9rifier si l'utilisateur a du00e9ju00e0 u00e9valu00e9 cette entreprise
$hasEvaluated = false;
if ($userLoggedIn && isset($this->userEvaluation)) {
    $hasEvaluated = true;
}
?>

<div class="evaluation-form">
    <h4><?php echo $hasEvaluated ? 'Modifier votre u00e9valuation' : 'u00c9valuer cette entreprise'; ?></h4>
    
    <?php if (!$userLoggedIn): ?>
        <p>Vous devez u00eatre <a href="index.php?action=login">connectu00e9</a> pour u00e9valuer cette entreprise.</p>
    <?php else: ?>
        <form action="index.php?action=store_evaluation&entreprise_id=<?php echo $this->entrepriseModel->id; ?>" method="post" id="evaluationForm">
            <?php if ($hasEvaluated): ?>
                <input type="hidden" name="evaluation_id" value="<?php echo htmlspecialchars($this->userEvaluation->id); ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="note">Note</label>
                <div class="rating-select">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" name="note" id="star<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo ($hasEvaluated && $this->userEvaluation->note == $i) ? 'checked' : ''; ?> required>
                        <label for="star<?php echo $i; ?>">u2605</label>
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
                <button type="submit" class="btn-submit"><?php echo $hasEvaluated ? 'Mettre u00e0 jour' : 'Soumettre'; ?></button>
            </div>
        </form>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('evaluationForm');
                
                if (form) {
                    form.addEventListener('submit', function(event) {
                        // Ru00e9initialise les messages d'erreur
                        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
                        
                        // Vu00e9rifie si une note a u00e9tu00e9 su00e9lectionnu00e9e
                        const noteSelected = form.querySelector('input[name="note"]:checked');
                        if (!noteSelected) {
                            document.getElementById('note-error').textContent = 'Veuillez attribuer une note';
                            event.preventDefault();
                        }
                        
                        // Vu00e9rifie si un commentaire a u00e9tu00e9 saisi
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
