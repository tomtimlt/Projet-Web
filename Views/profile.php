<!-- Views/profile.php -->
<?php require_once 'Templates/header.php'; ?>

<div class="container">
    <h1>Mon Profil</h1>
    <ul>
        <li><strong>Prénom :</strong> <?= htmlspecialchars($user['firstname'] ?? '') ?></li>
        <li><strong>Nom :</strong> <?= htmlspecialchars($user['lastname'] ?? '') ?></li>
        <li><strong>Email :</strong> <?= htmlspecialchars($user['email'] ?? '') ?></li>
        <li><strong>Rôle :</strong> <?= htmlspecialchars($user['role'] ?? '') ?></li>
    </ul>

    <?php if ($user['role'] === 'etudiant'): ?>
        <h3>Espace Étudiant</h3>


    <?php elseif ($user['role'] === 'pilote'): ?>
        <h3>Espace Pilote</h3>
        <!-- détails spécifiques au pilote -->

    <?php elseif ($user['role'] === 'admin'): ?>
        <h3>Espace Administrateur</h3>
        <p>Accès administrateur complet au site.</p>

    <?php else: ?>
        <p>Aucune information supplémentaire disponible.</p>
    <?php endif; ?>
</div>

<?php require_once 'Templates/footer.php'; ?>
