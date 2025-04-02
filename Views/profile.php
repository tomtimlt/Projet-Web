<!-- Views/profile.php -->
<?php require_once 'Templates/header.php'; ?>

<div class="container py-4">
    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Informations Personnelles</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-placeholder mb-3">
                            <i class="fas fa-user-circle fa-5x text-secondary"></i>
                        </div>
                        <h3><?= htmlspecialchars($user['firstname'] ?? '') ?> <?= htmlspecialchars($user['lastname'] ?? '') ?></h3>
                        <span class="badge bg-info"><?= htmlspecialchars($user['role'] ?? '') ?></span>
                    </div>
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-envelope me-2 text-primary"></i>Email:</span>
                            <span class="text-muted"><?= htmlspecialchars($user['email'] ?? '') ?></span>
                        </li>
                        <?php if (isset($user['phone'])): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-phone me-2 text-primary"></i>Téléphone:</span>
                            <span class="text-muted"><?= htmlspecialchars($user['phone'] ?? '') ?></span>
                        </li>
                        <?php endif; ?>
                        <?php if (isset($user['center'])): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-building me-2 text-primary"></i>Centre:</span>
                            <span class="text-muted"><?= htmlspecialchars($user['center'] ?? '') ?></span>
                        </li>
                        <?php endif; ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-key me-2 text-primary"></i>Rôle:</span>
                            <span class="text-muted"><?= htmlspecialchars($user['role'] ?? '') ?></span>
                        </li>
                    </ul>
                    
                    <div class="d-grid gap-2 mt-3">
                        <a href="index.php?page=profile&action=edit" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Modifier mon profil
                        </a>
                        <a href="index.php?page=password&action=change" class="btn btn-outline-secondary">
                            <i class="fas fa-lock me-2"></i>Changer mon mot de passe
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Espace Utilisateur</h4>
                </div>
                <div class="card-body">
                    <?php if ($user['role'] === 'etudiant'): ?>
                        <h5><i class="fas fa-user-graduate me-2 text-primary"></i>Espace Étudiant</h5>
                        <hr>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-clipboard-list me-2 text-primary"></i>Mes Candidatures</h5>
                                        <p class="card-text">Retrouvez et gérez toutes vos candidatures en cours.</p>
                                        <a href="index.php?page=applications" class="btn btn-sm btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-heart me-2 text-danger"></i>Ma Wishlist</h5>
                                        <p class="card-text">Consultez les offres de stage que vous avez enregistrées.</p>
                                        <a href="index.php?page=wishlist" class="btn btn-sm btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-briefcase me-2 text-primary"></i>Mon Stage</h5>
                                <p class="card-text">Consultez les informations relatives à votre stage actuel ou passé.</p>
                                <a href="index.php?page=my-internship" class="btn btn-sm btn-primary">Voir les détails</a>
                            </div>
                        </div>

                    <?php elseif ($user['role'] === 'pilote'): ?>
                        <h5><i class="fas fa-user-tie me-2 text-primary"></i>Espace Pilote</h5>
                        <hr>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-users me-2 text-primary"></i>Mes Étudiants</h5>
                                        <p class="card-text">Gérez les étudiants sous votre responsabilité.</p>
                                        <a href="index.php?page=my-students" class="btn btn-sm btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-building me-2 text-primary"></i>Entreprises</h5>
                                        <p class="card-text">Consultez et gérez les entreprises partenaires.</p>
                                        <a href="index.php?page=companies" class="btn btn-sm btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-briefcase me-2 text-primary"></i>Offres de Stage</h5>
                                        <p class="card-text">Gérez les offres de stage pour vos étudiants.</p>
                                        <a href="index.php?page=offers" class="btn btn-sm btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-chart-bar me-2 text-primary"></i>Statistiques</h5>
                                        <p class="card-text">Consultez les statistiques et rapports.</p>
                                        <a href="index.php?page=statistics" class="btn btn-sm btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php elseif ($user['role'] === 'admin'): ?>
                        <h5><i class="fas fa-user-shield me-2 text-primary"></i>Espace Administrateur</h5>
                        <hr>
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-users me-2 text-primary"></i>Utilisateurs</h5>
                                        <p class="card-text">Gérez tous les utilisateurs du système.</p>
                                        <a href="index.php?page=users" class="btn btn-sm btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-building me-2 text-primary"></i>Entreprises</h5>
                                        <p class="card-text">Gérez les entreprises partenaires.</p>
                                        <a href="index.php?page=companies" class="btn btn-sm btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-briefcase me-2 text-primary"></i>Offres</h5>
                                        <p class="card-text">Gérez les offres de stage.</p>
                                        <a href="index.php?page=offers" class="btn btn-sm btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-chart-line me-2 text-primary"></i>Statistiques</h5>
                                        <p class="card-text">Rapports et statistiques globales.</p>
                                        <a href="index.php?page=statistics" class="btn btn-sm btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Aucune information supplémentaire disponible pour votre rôle.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-placeholder {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}
</style>

<?php require_once 'Templates/footer.php'; ?>
