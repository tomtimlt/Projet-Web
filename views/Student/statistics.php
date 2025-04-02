<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques des étudiants</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="p-4">
    <h1 class="mb-4">Statistiques globales des étudiants</h1>

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

    <!-- Tableau des statistiques -->
    <table class="table table-bordered w-50">
        <tr>
            <th>Total d'étudiants</th>
            <td><?= $totalStudents ?></td>
        </tr>
        <tr>
            <th>Étudiants actifs</th>
            <td><?= $activeStudents ?></td>
        </tr>
        <tr>
            <th>Étudiants inactifs</th>
            <td><?= $inactiveStudents ?></td>
        </tr>
        <tr>
            <th>Total des candidatures</th>
            <td><?= $totalApplications ?></td>
        </tr>
        <tr>
            <th>Candidatures en attente</th>
            <td><?= $pendingApplications ?></td>
        </tr>
        <tr>
            <th>Candidatures acceptées</th>
            <td><?= $acceptedApplications ?></td>
        </tr>
        <tr>
            <th>Candidatures refusées</th>
            <td><?= $rejectedApplications ?></td>
        </tr>
        <tr>
            <th>Candidatures retirées</th>
            <td><?= $withdrawnApplications ?></td>
        </tr>
        <tr>
            <th>Moyenne de candidatures par étudiant</th>
            <td><?= $averageApplications ?></td>
        </tr>
    </table>

    <a href="index.php?controller=Student&action=index" class="btn btn-secondary">↩ Retour à la liste des étudiants</a>
</body>
</html>
