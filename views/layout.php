<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Gestion de Stages' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="Public/css/style.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="index.php?page=home">Gestion de Stages</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <?php if (isset($auth) && $auth->isLoggedIn()): ?>
                            <?php if ($auth->hasRole(['admin', 'pilote'])): ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                        Entreprises
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="index.php?page=companies">Liste des entreprises</a></li>
                                        <?php if ($auth->hasPermission('SFx3')): ?>
                                            <li><a class="dropdown-item" href="index.php?page=companies&action=create">Ajouter une entreprise</a></li>
                                        <?php endif; ?>
                                        <?php if ($auth->hasPermission('SFx7')): ?>
                                            <li><a class="dropdown-item" href="index.php?page=companies&action=stats">Statistiques</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                        Offres de Stage
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="index.php?page=offers">Liste des offres</a></li>
                                        <?php if ($auth->hasPermission('SFx9')): ?>
                                            <li><a class="dropdown-item" href="index.php?page=offers&action=create">Ajouter une offre</a></li>
                                        <?php endif; ?>
                                        <?php if ($auth->hasPermission('SFx12')): ?>
                                            <li><a class="dropdown-item" href="index.php?page=offers&action=stats">Statistiques</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </li>
                            <?php endif; ?>
                            
                            <?php if ($auth->hasRole('admin')): ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                        Pilotes
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="index.php?page=pilots">Liste des pilotes</a></li>
                                        <li><a class="dropdown-item" href="index.php?page=pilots&action=create">Ajouter un pilote</a></li>
                                    </ul>
                                </li>
                            <?php endif; ?>
                            
                            <?php if ($auth->hasRole(['admin', 'pilote'])): ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                        Étudiants
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="index.php?page=students">Liste des étudiants</a></li>
                                        <li><a class="dropdown-item" href="index.php?page=students&action=create">Ajouter un étudiant</a></li>
                                        <li><a class="dropdown-item" href="index.php?page=students&action=stats">Statistiques</a></li>
                                    </ul>
                                </li>
                            <?php endif; ?>
                            
                            <?php if ($auth->hasRole('etudiant')): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="index.php?page=offers">Offres de Stage</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="index.php?page=wishlist">Ma Wishlist</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="index.php?page=applications">Mes Candidatures</a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                    
                    <ul class="navbar-nav">
                        <?php if (isset($auth) && $auth->isLoggedIn()): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <?= htmlspecialchars($auth->getUser()['firstname'] . ' ' . $auth->getUser()['lastname']) ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="index.php?page=profile">Mon Profil</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="index.php?page=logout">Déconnexion</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?page=login">Connexion</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-4">
        <?php if (isset($flashMessage)): ?>
            <div class="alert alert-<?= $flashMessage['type'] ?> alert-dismissible fade show" role="alert">
                <?= $flashMessage['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?= $content ?? '' ?>
    </main>

    <footer class="bg-light text-center text-lg-start mt-auto">
        <div class="container p-4">
            <p class="text-center">© <?= date('Y') ?> - Gestion de Stages</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="Public/js/script.js"></script>
</body>
</html>
