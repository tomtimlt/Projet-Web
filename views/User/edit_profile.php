<?php require_once 'views/Templates/header.php'; ?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Modifier mon profil</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            Merci de corriger les erreurs ci-dessous.
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control <?= isset($errors['nom']) ? 'is-invalid' : '' ?>" id="nom" name="nom" value="<?= htmlspecialchars($user['nom'] ?? '') ?>">
                            <?php if (isset($errors['nom'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['nom']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control <?= isset($errors['prenom']) ? 'is-invalid' : '' ?>" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>">
                            <?php if (isset($errors['prenom'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['prenom']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="text" class="form-control" id="telephone" name="telephone" value="<?= htmlspecialchars($user['telephone'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="centre" class="form-label">Centre</label>
                            <input type="text" class="form-control" id="centre" name="centre" value="<?= htmlspecialchars($user['centre'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="promotion" class="form-label">Promotion</label>
                            <input type="text" class="form-control" id="promotion" name="promotion" value="<?= htmlspecialchars($user['promotion'] ?? '') ?>">
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="index.php?page=profile" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/Templates/footer.php'; ?>
