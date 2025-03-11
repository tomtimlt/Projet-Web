<?php
$pageTitle = "Supprimer l'étudiant";
require_once __DIR__ . '/../Templates/header.php';
$auth = \Models\Auth::getInstance();
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h2 class="mb-0">Confirmation de suppression</h2>
                </div>
                <div class="card-body">
                    <!-- Affichage des messages flash -->
                    <?php require_once 'Views/Templates/flash.php'; ?>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Attention :</strong> Vous êtes sur le point de supprimer définitivement cet étudiant.
                        Cette action est irréversible.
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title text-danger">Étudiant à supprimer :</h4>
                            <p class="card-text">
                                <strong>Nom :</strong> <?= htmlspecialchars($student['lastname']) ?><br>
                                <strong>Prénom :</strong> <?= htmlspecialchars($student['firstname']) ?><br>
                                <strong>Email :</strong> <?= htmlspecialchars($student['email']) ?><br>
                            </p>
                            
                            <hr>
                            
                            <h5>Conséquences de la suppression :</h5>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item">
                                    <i class="fas fa-trash-alt text-danger me-2"></i>
                                    Toutes les candidatures de cet étudiant seront supprimées
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-trash-alt text-danger me-2"></i>
                                    Toutes les offres dans sa wishlist seront supprimées
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-trash-alt text-danger me-2"></i>
                                    Les informations personnelles de l'étudiant seront définitivement perdues
                                </li>
                            </ul>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Note : Si l'étudiant a des candidatures acceptées, la suppression échouera.
                            </div>
                        </div>
                    </div>
                    
                    <form action="index.php?controller=student&action=destroy" method="POST" class="mt-4">
                        <input type="hidden" name="id" value="<?= $student['id'] ?>">
                        
                        <div class="d-flex justify-content-between">
                            <a href="index.php?controller=student&action=show&id=<?= $student['id'] ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt"></i> Confirmer la suppression
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'Views/Templates/footer.php'; ?>
