<?php include_once __DIR__ . '/../Templates/header.php'; ?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?page=pilots">Gestion des pilotes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Confirmer la suppression</li>
        </ol>
    </nav>

    <div class="card border-danger shadow-sm">
        <div class="card-header bg-danger text-white">
            <h2 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Confirmer la suppression</h2>
        </div>
        <div class="card-body">
            <div class="alert alert-warning mb-4">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Attention :</strong> Cette action est irréversible et pourrait avoir des conséquences importantes.
            </div>

            <p class="lead">Êtes-vous sûr de vouloir supprimer définitivement le compte pilote suivant ?</p>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nom :</strong> <?= htmlspecialchars($pilot['lastname']) ?></p>
                            <p><strong>Prénom :</strong> <?= htmlspecialchars($pilot['firstname']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Email :</strong> <?= htmlspecialchars($pilot['email']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <p class="text-danger"><strong>Conséquences possibles :</strong></p>
            <ul class="text-danger mb-4">
                <li>Le pilote ne pourra plus se connecter à la plateforme</li>
                <li>Les entreprises associées à ce pilote seront désassociées</li>
                <li>Toutes les activités liées à ce compte seront perdues</li>
            </ul>

            <div class="d-flex justify-content-center gap-3">
                <a href="index.php?page=pilots" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left me-1"></i>Annuler
                </a>
                <form action="index.php?page=pilots&action=delete" method="post" style="display: inline;">
                    <input type="hidden" name="id" value="<?= $pilot['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-lg">
                        <i class="fas fa-trash-alt me-1"></i>Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../Templates/footer.php'; ?>
