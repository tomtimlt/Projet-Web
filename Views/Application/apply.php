<?php
$pageTitle = "Postuler à une offre";
require_once __DIR__ . '/../Templates/header.php';
$auth = \Models\Auth::getInstance();
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?page=offers">Offres</a></li>
            <li class="breadcrumb-item"><a href="index.php?page=offers&action=view&id=<?= $offer['id'] ?>">Détail de l'offre</a></li>
            <li class="breadcrumb-item active" aria-current="page">Postuler</li>
        </ol>
    </nav>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h1 class="h4 mb-0">Postuler à l'offre : <?= htmlspecialchars($offer['title']) ?></h1>
        </div>
        <div class="card-body">
            <!-- Affichage des messages flash -->
            <?php require_once 'Views/Templates/flash.php'; ?>

            <!-- Résumé de l'offre -->
            <div class="alert alert-info mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong><i class="fas fa-building"></i> Entreprise :</strong> <?= htmlspecialchars($offer['company_name']) ?></p>
                        <p><strong><i class="fas fa-map-marker-alt"></i> Lieu :</strong> <?= htmlspecialchars($offer['location']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong><i class="fas fa-calendar-alt"></i> Période :</strong> <?= date('d/m/Y', strtotime($offer['start_date'])) ?> - <?= date('d/m/Y', strtotime($offer['end_date'])) ?></p>
                        <p><strong><i class="fas fa-euro-sign"></i> Rémunération :</strong> <?= $offer['salary'] ? htmlspecialchars($offer['salary']) . ' €/mois' : 'Non précisée' ?></p>
                    </div>
                </div>
            </div>

            <!-- Formulaire de candidature -->
            <form action="index.php?page=applications&action=store" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                <input type="hidden" name="offer_id" value="<?= $offer['id'] ?>">
                
                <div class="mb-4">
                    <label for="motivation" class="form-label fw-bold">Lettre de motivation <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="motivation" name="motivation" rows="10" required minlength="100"><?= isset($_POST['motivation']) ? htmlspecialchars($_POST['motivation']) : '' ?></textarea>
                    <div class="form-text">
                        Expliquez pourquoi vous êtes intéressé(e) par cette offre et ce que vous pouvez apporter à l'entreprise.
                        Minimum 100 caractères.
                    </div>
                    <div class="invalid-feedback">
                        Veuillez fournir une lettre de motivation (minimum 100 caractères).
                    </div>
                </div>

                <div class="mb-4">
                    <label for="cv_file" class="form-label fw-bold">CV (format PDF) <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="cv_file" name="cv_file" accept="application/pdf" required>
                    <div class="form-text">
                        Votre CV au format PDF uniquement. Taille maximale : 5 Mo.
                    </div>
                    <div class="invalid-feedback">
                        Veuillez fournir votre CV au format PDF.
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="index.php?page=offers&action=view&id=<?= $offer['id'] ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane"></i> Envoyer ma candidature
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript pour la validation côté client -->
<script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function () {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>

<?php require_once 'Views/Templates/footer.php'; ?>
