<?php
$pageTitle = "Détails de l'étudiant";
require_once __DIR__ . '/../Templates/header.php';
$auth = \Models\Auth::getInstance();
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- Affichage des messages flash -->
            <?php require_once 'Views/Templates/flash.php'; ?>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Profil étudiant</h2>
                    <div>
                        <?php if ($auth->hasRole(['admin', 'pilote'])) : ?>
                            <a href="index.php?controller=student&action=edit&id=<?= $student['id'] ?>" class="btn btn-light btn-sm">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                        <?php endif; ?>
                        <?php if ($auth->hasRole(['admin'])) : ?>
                            <a href="index.php?controller=student&action=delete&id=<?= $student['id'] ?>" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Supprimer
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h3 class="text-primary">
                                <?= htmlspecialchars($student['firstname'] . ' ' . $student['lastname']) ?>
                            </h3>
                            <p class="text-muted">
                                <i class="fas fa-envelope"></i> <?= htmlspecialchars($student['email']) ?>
                            </p>
                            <p class="text-muted">
                                <i class="fas fa-calendar-alt"></i> Inscrit le <?= date('d/m/Y', strtotime($student['created_at'])) ?>
                            </p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h4 class="mb-3">Statistiques de recherche de stage</h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-header">Candidatures</div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Total :</span>
                                        <span class="badge bg-primary"><?= $stats['applications']['total'] ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>En attente :</span>
                                        <span class="badge bg-warning"><?= $stats['applications']['pending'] ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Acceptées :</span>
                                        <span class="badge bg-success"><?= $stats['applications']['accepted'] ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Refusées :</span>
                                        <span class="badge bg-danger"><?= $stats['applications']['rejected'] ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Retirées :</span>
                                        <span class="badge bg-secondary"><?= $stats['applications']['withdrawn'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-header">Wishlist</div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Offres enregistrées :</span>
                                        <span class="badge bg-info"><?= $stats['wishlist']['total'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Boutons d'action -->
                    <div class="mt-4 d-flex justify-content-between">
                        <a href="index.php?controller=student&action=index" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                        
                        <!-- Liens vers les candidatures et wishlist -->
                        <div>
                            <a href="index.php?controller=application&action=byStudent&id=<?= $student['id'] ?>" class="btn btn-primary">
                                <i class="fas fa-file-alt"></i> Voir les candidatures
                            </a>
                            <a href="index.php?controller=wishlist&action=byStudent&id=<?= $student['id'] ?>" class="btn btn-info">
                                <i class="fas fa-bookmark"></i> Voir la wishlist
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'Views/Templates/footer.php'; ?>
