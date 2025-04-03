<?php
/**
 * Vue du profil utilisateur
 * Affiche les informations du profil de l'utilisateur connecté
 */

// Protection contre l'accès direct
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

// Récupération des données de l'utilisateur
$userId = $_SESSION['user_id'];
$userModel->readOne($userId);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header>
    <div class="container">
        <nav>
            <ul>
                <li><a href="index.php?action=home">Accueil</a></li>
                <li><a href="index.php?action=entreprises">Entreprises</a></li>
                <li><a href="index.php?action=profile" class="active">Mon Profil</a></li>
                <li><a href="index.php?action=logout">Déconnexion</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <div class="container">
        <section class="profile-section">
            <h1>Profil Utilisateur</h1>

            <div class="profile-info">
                <div class="info-group">
                    <h2>Informations Personnelles</h2>
                    <div class="info-row">
                        <span class="label">Nom :</span>
                        <span class="value"><?= htmlspecialchars($userModel->name) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Email :</span>
                        <span class="value"><?= htmlspecialchars($userModel->email) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Rôle :</span>
                        <span class="value"><?= htmlspecialchars($userModel->role) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Compte créé le :</span>
                        <span class="value"><?= htmlspecialchars($userModel->created_at) ?></span>
                    </div>
                </div>
            </div>

            <div class="actions">
                <a href="index.php?action=edit_profile" class="button">Modifier mon profil</a>
                <a href="index.php?action=change_password" class="button">Changer mon mot de passe</a>
            </div>
        </section>
    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; <?= date('Y') ?> Gestion de Stage. Tous droits réservés.</p>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Script pour les interactions du profil utilisateur
        console.log('Page de profil chargée');
    });
</script>
</body>
</html>
