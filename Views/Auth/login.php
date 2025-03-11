<?php
$pageTitle = 'Connexion';
$email = $data['email'] ?? '';
$errors = $data['errors'] ?? [];

// Démarrer la capture du contenu pour l'insérer dans le layout
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">Connexion</h2>
            </div>
            <div class="card-body">
                <?php if (isset($errors['auth'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($errors['auth']) ?>
                    </div>
                <?php endif; ?>
                
                <form action="index.php?page=authenticate" method="post" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse email</label>
                        <input 
                            type="email" 
                            class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                            id="email" 
                            name="email" 
                            value="<?= htmlspecialchars($email) ?>" 
                            required 
                            autofocus
                        >
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['email']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input 
                            type="password" 
                            class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                            id="password" 
                            name="password" 
                            required
                        >
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['password']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Se connecter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Récupérer le contenu capturé et le stocker dans la variable $content
$content = ob_get_clean();

// Inclure le layout qui utilisera la variable $content
require_once __DIR__ . '/../layout.php';
?>
