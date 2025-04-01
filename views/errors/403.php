<?php include 'views/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h3 class="m-0"><i class="fas fa-exclamation-triangle me-2"></i>Accès refusé</h3>
                </div>
                <div class="card-body text-center py-5">
                    <h1 class="display-1 text-danger">403</h1>
                    <h2 class="mb-4">Permission refusée</h2>
                    
                    <p class="lead">
                        <?php echo isset($_SESSION['error']) ? htmlspecialchars($_SESSION['error']) : "Vous n'avez pas les permissions nécessaires pour accéder à cette ressource."; ?>
                    </p>
                    
                    <hr class="my-4">
                    
                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>
