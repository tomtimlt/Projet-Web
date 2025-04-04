<?php include_once __DIR__ . '/../Templates/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-user-graduate me-2"></i>Supprimer un étudiant</h1>
        <a href="index.php?page=students" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Retour à la liste
        </a>
    </div>
    
    <?php if ($student): ?>
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Confirmation de suppression</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <p class="mb-1">Êtes-vous sûr de vouloir supprimer le compte de l'étudiant :</p>
                    <p class="h4 mb-3 text-center">
                        <strong><?= htmlspecialchars($student['firstname'] . " " . $student['lastname']) ?></strong>
                        <small class="d-block text-muted"><?= htmlspecialchars($student['email']) ?></small>
                    </p>
                    <p class="text-danger mb-0">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        <strong>Attention :</strong> Cette action est irréversible et supprimera également toutes les données associées à cet étudiant.
                    </p>
                </div>
                
                <form method="post" action="index.php?page=student_destroy">
    <input type="hidden" name="id" value="<?= $student['id'] ?>">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
    <div class="d-flex justify-content-between mt-4">
        <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash me-1"></i>Supprimer définitivement
        </button>
        <a href="index.php?page=students" class="btn btn-outline-secondary">
            <i class="fas fa-times me-1"></i>Annuler
        </a>
    </div>
</form>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Erreur</h5>
            <p class="mb-0">Étudiant introuvable. Il a peut-être déjà été supprimé ou l'identifiant est invalide.</p>
        </div>
        <div class="text-center mt-4">
            <a href="index.php?page=students" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i>Retour à la liste des étudiants
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../Templates/footer.php'; ?>
