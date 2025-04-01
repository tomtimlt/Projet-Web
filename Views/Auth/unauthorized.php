<?php
$pageTitle = 'Accès Refusé';

// Démarrer la capture du contenu pour l'insérer dans le layout
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-danger text-white">
                <h2 class="mb-0">Accès Refusé</h2>
            </div>
            <div class="card-body text-center">
                <div class="mb-4">
                    <i class="fas fa-exclamation-triangle fa-4x text-danger"></i>
                </div>
                <h3>Vous n'avez pas les droits nécessaires pour accéder à cette page</h3>
                <p class="lead mt-3">Veuillez contacter un administrateur si vous pensez que c'est une erreur.</p>
                <div class="mt-4">
                    <a href="index.php?page=home" class="btn btn-primary">Retour à l'accueil</a>
                </div>
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
