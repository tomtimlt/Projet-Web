<?php
// Titre de la page
$pageTitle = "Accueil";

// Inclusion du header
require_once __DIR__ . '/Templates/header.php';
?>

<div class="container py-4">
    <!-- Section de présentation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h1 class="display-5 fw-bold mb-3">Bienvenue sur votre plateforme de gestion de stages</h1>
                            <p class="lead mb-4">Notre système centralisé vous permet de gérer efficacement tous les aspects des stages, de la recherche d'offres à la validation finale.</p>
                            <p class="mb-4">
                                Cette plateforme a été conçue pour faciliter la collaboration entre les étudiants, les pilotes et les entreprises partenaires. 
                                Que vous soyez à la recherche d'un stage, un pilote accompagnant des étudiants ou un administrateur, vous trouverez ici tous les outils nécessaires pour mener à bien vos missions.
                            </p>
                            
                            <?php if (!isset($user)): ?>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                                <a href="index.php?page=login" class="btn btn-primary btn-lg px-4 me-md-2">Se connecter</a>
                                <a href="index.php?page=register" class="btn btn-outline-secondary btn-lg px-4">S'inscrire</a>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-lg-4 text-center d-none d-lg-block">
                            <i class="fas fa-laptop-code text-primary" style="font-size: 180px; opacity: 0.8;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section caractéristiques -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-center mb-4">Comment ça fonctionne</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-primary bg-gradient text-white rounded-circle mb-3">
                                <i class="fas fa-search"></i>
                            </div>
                            <h3 class="fs-5">Recherche de stages</h3>
                            <p>Trouvez facilement des offres de stage correspondant à votre profil et vos compétences.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-primary bg-gradient text-white rounded-circle mb-3">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h3 class="fs-5">Gestion des candidatures</h3>
                            <p>Suivez vos candidatures et obtenez des retours en temps réel sur leur avancement.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-primary bg-gradient text-white rounded-circle mb-3">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3 class="fs-5">Suivi et évaluation</h3>
                            <p>Gérez efficacement le suivi de vos stages et les évaluations associées.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

<style>
.feature-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    font-size: 24px;
}
</style>

<?php
// Inclusion du footer
require_once __DIR__ . '/Templates/footer.php';
?>
