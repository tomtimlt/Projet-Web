<?php
// Titre de la page
$pageTitle = htmlspecialchars($company['name']);

// Inclusion du header
require_once __DIR__ . '/../Templates/header.php';
?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h2 class="mb-0"><?= htmlspecialchars($company['name']) ?></h2>
                <div>
                    <a href="index.php?page=companies" class="btn btn-light me-2">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <?php if ($this->auth->hasPermission('SFx4')): ?>
                    <a href="index.php?page=companies&action=edit&id=<?= $company['id'] ?>" class="btn btn-light">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Informations principales -->
                    <div class="col-md-8">
                        <h3 class="border-bottom pb-2 mb-3">Informations</h3>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Adresse:</strong><br>
                                <?= htmlspecialchars($company['address']) ?><br>
                                <?= htmlspecialchars($company['postal_code']) ?> <?= htmlspecialchars($company['city']) ?><br>
                                <?= htmlspecialchars($company['country']) ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($company['phone'])): ?>
                                <p><strong>Téléphone:</strong> <?= htmlspecialchars($company['phone']) ?></p>
                                <?php endif; ?>
                                
                                <?php if (!empty($company['email'])): ?>
                                <p><strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($company['email']) ?>"><?= htmlspecialchars($company['email']) ?></a></p>
                                <?php endif; ?>
                                
                                <?php if (!empty($company['website'])): ?>
                                <p><strong>Site web:</strong> <a href="<?= htmlspecialchars($company['website']) ?>" target="_blank"><?= htmlspecialchars($company['website']) ?></a></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if (!empty($company['description'])): ?>
                        <div class="mb-4">
                            <h4>Description</h4>
                            <p><?= nl2br(htmlspecialchars($company['description'])) ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Secteur d'activité:</strong> <?= htmlspecialchars($company['sector'] ?? 'Non spécifié') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Taille de l'entreprise:</strong> <?= htmlspecialchars($company['size'] ?? 'Non spécifiée') ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Statistiques et notation -->
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header bg-info text-white">
                                <h4 class="mb-0">Statistiques</h4>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <h5>Note moyenne</h5>
                                    <?php if ($company['average_rating']): ?>
                                        <div class="display-4 fw-bold text-warning"><?= number_format($company['average_rating'], 1) ?>/5</div>
                                        <div class="fs-2 mb-3">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?php if ($i <= round($company['average_rating'])): ?>
                                                    <i class="fas fa-star text-warning"></i>
                                                <?php elseif ($i - 0.5 <= $company['average_rating']): ?>
                                                    <i class="fas fa-star-half-alt text-warning"></i>
                                                <?php else: ?>
                                                    <i class="far fa-star text-warning"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">Aucune évaluation</p>
                                    <?php endif; ?>
                                </div>
                                
                                <p><i class="fas fa-briefcase"></i> <strong>Offres de stage:</strong> <span class="badge bg-primary"><?= (int)$company['offer_count'] ?></span></p>
                                
                                <?php if (!empty($ratings)): ?>
                                <p><i class="fas fa-comment"></i> <strong>Évaluations:</strong> <span class="badge bg-success"><?= count($ratings) ?></span></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Ajout d'une évaluation -->
                        <?php if ($this->auth->hasPermission('SFx5') && !$hasRated): ?>
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h4 class="mb-0">Évaluer cette entreprise</h4>
                                </div>
                                <div class="card-body">
                                    <form action="index.php?page=companies&action=rate" method="post">
                                        <input type="hidden" name="entreprise_id" value="<?= $company['id'] ?>">

                                        <div class="mb-3">
                                            <label class="form-label">Note</label>
                                            <div class="star-rating">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" required <?= $i === 5 ? 'checked' : '' ?>>
                                                    <label for="star<?= $i ?>"><i class="fas fa-star"></i></label>
                                                <?php endfor; ?>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Commentaire</label>
                                            <textarea class="form-control" name="commentaire" rows="3" placeholder="Partagez votre expérience avec cette entreprise..."></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-paper-plane"></i> Envoyer l'évaluation
                                        </button>
                                    </form>

                                </div>
                            </div>
                        <?php elseif ($this->auth->hasPermission('SFx5') && $hasRated): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Vous avez déjà évalué cette entreprise.
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Évaluations -->
    <?php if (!empty($ratings)): ?>
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Évaluations (<?= count($ratings) ?>)</h3>
            </div>
            <div class="card-body">
                <?php foreach ($ratings as $rating): ?>
                <div class="border-bottom mb-4 pb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong><?= htmlspecialchars($rating['firstname'] . ' ' . $rating['lastname']) ?></strong>
                            <span class="badge bg-secondary ms-2"><?= htmlspecialchars($rating['role']) ?></span>
                        </div>
                        <div>
                            <span class="text-muted"><?= date('d/m/Y', strtotime($rating['created_at'])) ?></span>
                            
                            <?php 
                            // Afficher le bouton de suppression si l'utilisateur est admin ou si c'est son propre commentaire
                            $canDelete = $this->auth->isLoggedIn() && 
                                        ($this->auth->getUser()['role'] === 'admin' || 
                                         $this->auth->getUser()['id'] === $rating['user_id']);
                            ?>
                            
                            <?php if ($canDelete): ?>
                            <a href="index.php?page=companies&action=deleteRating&rating_id=<?= $rating['id'] ?>&company_id=<?= $company['id'] ?>" 
                               class="btn btn-sm btn-danger ms-2" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette évaluation ?');">
                                <i class="fas fa-trash"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?php if ($i <= $rating['rating']): ?>
                                <i class="fas fa-star text-warning"></i>
                            <?php else: ?>
                                <i class="far fa-star text-warning"></i>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <span class="ms-2"><?= $rating['rating'] ?>/5</span>
                    </div>
                    
                    <?php if (!empty($rating['comment'])): ?>
                    <div>
                        <?= nl2br(htmlspecialchars($rating['comment'])) ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
    /* Style pour le système d'évaluation */
    .star-rating {
        display: inline-flex;
        flex-direction: row-reverse;
    }
    
    .star-rating input {
        display: none;
    }
    
    .star-rating label {
        cursor: pointer;
        font-size: 1.5rem;
        color: #ddd;
        padding: 0 0.1rem;
    }
    
    .star-rating label:hover,
    .star-rating label:hover ~ label,
    .star-rating input:checked ~ label {
        color: #FFD700;
    }
</style>

<?php
// Inclusion du footer
require_once __DIR__ . '/../Templates/footer.php';
?>
