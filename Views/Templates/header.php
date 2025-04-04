<?php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Obtenir l'instance Auth
$auth = \Models\Auth::getInstance();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?>Gestion des stages</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="Public/css/style.css">
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-graduation-cap me-2"></i>Gestion des stages
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['page'] ?? '') === '' ? 'active' : '' ?>" href="index.php">
                            <i class="fas fa-home me-1"></i>Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['page'] ?? '') === 'companies' ? 'active' : '' ?>" href="index.php?page=companies">
                            <i class="fas fa-building me-1"></i>Entreprises
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['page'] ?? '') === 'offers' ? 'active' : '' ?>" href="index.php?page=offers">
                            <i class="fas fa-briefcase me-1"></i>Offres de stage
                        </a>
                    </li>
                    <?php if (isset($_SESSION['user']) && isset($_SESSION['auth']) && $_SESSION['auth'] === true): ?>
                        <?php if ($auth->hasRole(['etudiant'])): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle <?= in_array(($_GET['page'] ?? ''), ['wishlist', 'applications']) ? 'active' : '' ?>" href="#" id="studentDropdown" role="button"
                                   data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user-graduate me-1"></i>Espace étudiant
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="studentDropdown">
                                    <li>
                                        <a class="dropdown-item <?= ($_GET['page'] ?? '') === 'wishlist' ? 'active' : '' ?>" href="index.php?page=wishlist">
                                            <i class="fas fa-heart me-1"></i>Ma liste de souhaits
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item <?= ($_GET['page'] ?? '') === 'applications' ? 'active' : '' ?>" href="index.php?page=applications">
                                            <i class="fas fa-paper-plane me-1"></i>Mes candidatures
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if ($auth->hasRole(['admin', 'pilote'])): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="statsDropdown" role="button"
                                   data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-chart-bar me-1"></i>Statistiques
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="statsDropdown">
                                    <?php if ($auth->hasRole(['admin', 'pilote'])): ?>
                                        <li>
                                            <a class="dropdown-item" href="index.php?page=companies&action=stats">
                                                <i class="fas fa-building me-1"></i>Statistiques entreprises
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="index.php?page=offers&action=statistics">
                                                <i class="fas fa-briefcase me-1"></i>Statistiques offres
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if ($auth->hasRole(['admin'])): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle <?= in_array(($_GET['page'] ?? ''), ['applications', 'wishlist']) ? 'active' : '' ?>" href="#" id="adminDropdown" role="button"
                                   data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user-shield me-1"></i>Administration
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                    <li>
                                        <a class="dropdown-item <?= ($_GET['page'] ?? '') === 'applications' ? 'active' : '' ?>" href="index.php?page=applications">
                                            <i class="fas fa-paper-plane me-1"></i>Candidatures des étudiants
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item <?= ($_GET['page'] ?? '') === 'wishlist' ? 'active' : '' ?>" href="index.php?page=wishlist">
                                            <i class="fas fa-heart me-1"></i>Wishlists des étudiants
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user']) && isset($_SESSION['auth'])): ?>
                        <!-- Si l'utilisateur est connecté -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i>
                                <?= htmlspecialchars($_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['lastname']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="index.php?page=profile">
                                        <i class="fas fa-user-cog me-1"></i>Mon profil
                                    </a>
                                </li>
                                <?php if ($auth->hasRole(['admin'])): ?>
                                    <li>
                                    </li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="index.php?page=logout">
                                        <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
                                    </a>
                                </li>
                            </ul>
                        </li>

                    <?php else: ?>
                        <!-- Si l'utilisateur n'est pas connecté -->
                        <li class="nav-item">
                            <a class="nav-link <?= ($_GET['page'] ?? '') === 'login' ? 'active' : '' ?>" href="index.php?page=login">
                                <i class="fas fa-sign-in-alt me-1"></i>Connexion
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Conteneur principal -->
    <main class="container-fluid py-3">
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['flash']['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
