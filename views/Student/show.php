<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails de l'étudiant</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="p-4">
    <h1 class="mb-4">Détails de l'étudiant</h1>

    <!-- Messages flash -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Informations de l'étudiant -->
    <ul class="list-group mb-3">
        <li class="list-group-item"><strong>ID :</strong> <?= htmlspecialchars($student['id']) ?></li>
        <li class="list-group-item"><strong>Prénom :</strong> <?= htmlspecialchars($student['firstname']) ?></li>
        <li class="list-group-item"><strong>Nom :</strong> <?= htmlspecialchars($student['lastname']) ?></li>
        <li class="list-group-item"><strong>Email :</strong> <?= htmlspecialchars($student['email']) ?></li>
        <li class="list-group-item"><strong>Statut du compte :</strong> <?= $student['is_active'] ? 'Actif' : 'Inactif' ?></li>
        <li class="list-group-item"><strong>Compte créé le :</strong> 
            <?php 
            $dateC = date("d/m/Y à H:i", strtotime($student['created_at']));
            echo htmlspecialchars($dateC);
            ?>
        </li>
        <li class="list-group-item"><strong>Dernière mise à jour le :</strong> 
            <?php 
            $dateU = date("d/m/Y à H:i", strtotime($student['updated_at']));
            echo htmlspecialchars($dateU);
            ?>
        </li>
    </ul>

    <!-- Boutons d'action -->
    <a href="index.php?controller=Student&action=index" class="btn btn-secondary">Retour à la liste</a>
    <a href="index.php?controller=Student&action=edit&id=<?= $student['id'] ?>" class="btn btn-primary">Éditer</a>
    <a href="index.php?controller=Student&action=delete&id=<?= $student['id'] ?>" class="btn btn-danger">Supprimer</a>
</body>
</html>
