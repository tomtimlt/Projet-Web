<?php include_once __DIR__ . '/../Templates/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-user-graduate me-2"></i><?= $pageTitle ?></h1>
        <a href="index.php?page=students&action=create" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Nouvel étudiant
        </a>
    </div>
    
    <!-- Recherche -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-search me-2"></i>Rechercher des étudiants</h5>
        </div>
        <div class="card-body">
            <form action="index.php" method="GET" id="searchForm">
                <input type="hidden" name="page" value="students">
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Rechercher par nom, prénom ou email" 
                                   value="<?= htmlspecialchars($search ?? '') ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <?php if (!empty($search)): ?>
                            <a href="index.php?page=students" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Réinitialiser
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Résultats -->
    <div class="card">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Liste des étudiants</h5>
                <span class="badge bg-primary"><?= $totalStudents ?> étudiant(s)</span>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($students)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Aucun étudiant trouvé.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Email</th>
                                <th>Statut</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $stu): ?>
                                <tr>
                                    <td><?= htmlspecialchars($stu['lastname']) ?></td>
                                    <td><?= htmlspecialchars($stu['firstname']) ?></td>
                                    <td><?= htmlspecialchars($stu['email']) ?></td>
                                    <td>
                                        <span class="badge <?= $stu['is_active'] ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $stu['is_active'] ? 'Actif' : 'Inactif' ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="index.php?page=students&action=edit&id=<?= $stu['id'] ?>" 
                                               class="btn btn-outline-primary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="index.php?page=students&action=confirm-delete&id=<?= $stu['id'] ?>" 
                                               class="btn btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <!-- Bouton précédent -->
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="index.php?page=students&page_num=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            
                            <!-- Pages numérotées -->
                            <?php
                            $startPage = max(1, $page - 2);
                            $endPage = min($startPage + 4, $totalPages);
                            
                            if ($endPage - $startPage < 4 && $startPage > 1) {
                                $startPage = max(1, $endPage - 4);
                            }
                            
                            for ($i = $startPage; $i <= $endPage; $i++):
                            ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="index.php?page=students&page_num=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <!-- Bouton suivant -->
                            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="index.php?page=students&page_num=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../Templates/footer.php'; ?>
