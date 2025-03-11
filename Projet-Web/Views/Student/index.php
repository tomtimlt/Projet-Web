<?php
$pageTitle = "Gestion des étudiants";
require_once __DIR__ . '/../Templates/header.php';
$auth = \Models\Auth::getInstance();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Liste des étudiants</h1>
        <?php if ($auth->hasRole(['admin', 'pilote'])) : ?>
            <a href="index.php?page=students&action=create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter un étudiant
            </a>
        <?php endif; ?>
    </div>

    <!-- Affichage des messages flash -->
    <?php require_once 'Views/Templates/flash.php'; ?>

    <!-- Formulaire de recherche -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Rechercher</h5>
        </div>
        <div class="card-body">
            <form action="index.php" method="GET" class="row g-3">
                <input type="hidden" name="page" value="students">
                
                <div class="col-md-4">
                    <label for="firstname" class="form-label">Prénom</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" 
                           value="<?= isset($_GET['firstname']) ? htmlspecialchars($_GET['firstname']) : '' ?>">
                </div>
                
                <div class="col-md-4">
                    <label for="lastname" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="lastname" name="lastname"
                           value="<?= isset($_GET['lastname']) ? htmlspecialchars($_GET['lastname']) : '' ?>">
                </div>
                
                <div class="col-md-4">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="<?= isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '' ?>">
                </div>
                
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    <a href="index.php?page=students" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="mb-4">
        <a href="index.php?page=students&action=stats" class="btn btn-info">
            <i class="fas fa-chart-bar"></i> Voir les statistiques détaillées
        </a>
    </div>

    <!-- Tableau des étudiants -->
    <?php if (empty($students)) : ?>
        <div class="alert alert-info">
            Aucun étudiant trouvé.
        </div>
    <?php else : ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Candidatures</th>
                        <th>Wishlist</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student) : ?>
                        <tr>
                            <td><?= htmlspecialchars($student['id']) ?></td>
                            <td><?= htmlspecialchars($student['lastname']) ?></td>
                            <td><?= htmlspecialchars($student['firstname']) ?></td>
                            <td><?= htmlspecialchars($student['email']) ?></td>
                            <td>
                                <span class="badge bg-primary">
                                    <?= htmlspecialchars($student['application_count']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    <?= htmlspecialchars($student['wishlist_count']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="index.php?page=students&action=view&id=<?= $student['id'] ?>" 
                                       class="btn btn-sm btn-info" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($auth->hasRole(['admin', 'pilote'])) : ?>
                                        <a href="index.php?page=students&action=edit&id=<?= $student['id'] ?>" 
                                           class="btn btn-sm btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($auth->hasRole(['admin'])) : ?>
                                        <a href="index.php?page=students&action=delete&id=<?= $student['id'] ?>" 
                                           class="btn btn-sm btn-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- Pagination (simple) -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php 
            $currentPage = isset($_GET['page_num']) ? intval($_GET['page_num']) : 1;
            $prevPage = max(1, $currentPage - 1);
            $nextPage = $currentPage + 1;
            
            // Construire l'URL avec les paramètres de recherche
            $queryParams = $_GET;
            $queryParams['page'] = 'students';
            unset($queryParams['page_num']);
            $queryString = http_build_query($queryParams);
            $baseUrl = 'index.php?' . $queryString . '&page_num=';
            ?>
            
            <li class="page-item <?= ($currentPage == 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= $baseUrl . $prevPage ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li class="page-item active">
                <span class="page-link"><?= $currentPage ?></span>
            </li>
            <li class="page-item <?= (count($students) < 10) ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= $baseUrl . $nextPage ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</div>

<?php require_once 'Views/Templates/footer.php'; ?>
