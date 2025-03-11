<?php
// Titre de la page
$pageTitle = "Accueil";

// Inclusion du header
require_once __DIR__ . '/Templates/header.php';
?>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Bienvenue<?= isset($user) ? ', ' . htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) : '' ?></h2>
                </div>
                <div class="card-body">
                    <?php if (isset($user)): ?>
                        <p class="lead">Vous êtes connecté en tant que <strong><?= htmlspecialchars($this->auth->getPermissions()['roles'][$user['role']]) ?></strong>.</p>
                        
                        <div class="mt-4">
                            <h3>Accès rapide</h3>
                            <div class="row mt-3">
                                <?php if ($this->auth->hasRole(['admin', 'pilote'])): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h5 class="card-title">Gestion des Entreprises</h5>
                                                <p class="card-text">Consultez et gérez les entreprises partenaires.</p>
                                                <a href="index.php?page=companies" class="btn btn-outline-primary">Accéder</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h5 class="card-title">Gestion des Offres</h5>
                                                <p class="card-text">Consultez et gérez les offres de stage.</p>
                                                <a href="index.php?page=offers" class="btn btn-outline-primary">Accéder</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h5 class="card-title">Gestion des Étudiants</h5>
                                                <p class="card-text">Consultez et gérez les comptes étudiants.</p>
                                                <a href="index.php?page=students" class="btn btn-outline-primary">Accéder</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($this->auth->hasRole('admin')): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h5 class="card-title">Gestion des Pilotes</h5>
                                                <p class="card-text">Consultez et gérez les comptes pilotes.</p>
                                                <a href="index.php?page=pilots" class="btn btn-outline-primary">Accéder</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($this->auth->hasRole('etudiant')): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h5 class="card-title">Offres de Stage</h5>
                                                <p class="card-text">Consultez les offres de stage disponibles.</p>
                                                <a href="index.php?page=offers" class="btn btn-outline-primary">Accéder</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h5 class="card-title">Ma Wishlist</h5>
                                                <p class="card-text">Gérez vos offres favorites.</p>
                                                <a href="index.php?page=wishlist" class="btn btn-outline-primary">Accéder</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h5 class="card-title">Mes Candidatures</h5>
                                                <p class="card-text">Suivez vos candidatures en cours.</p>
                                                <a href="index.php?page=applications" class="btn btn-outline-primary">Accéder</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="lead">Veuillez vous connecter pour accéder à toutes les fonctionnalités du système de gestion de stages.</p>
                        <a href="index.php?page=login" class="btn btn-primary mt-3">Se connecter</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Inclusion du footer
require_once __DIR__ . '/Templates/footer.php';
?>
