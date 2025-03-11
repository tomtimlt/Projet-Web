<?php include_once __DIR__ . '/../Templates/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Administration des wishlists</h1>
    </div>

    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-heart"></i> Wishlists des étudiants</h5>
        </div>
        <div class="card-body">
            <?php if (empty($wishlistsByStudent)): ?>
                <div class="alert alert-info">
                    Aucune wishlist trouvée.
                </div>
            <?php else: ?>
                <div class="accordion" id="wishlistAccordion">
                    <?php foreach ($wishlistsByStudent as $studentId => $wishlistData): ?>
                        <?php $student = $wishlistData['student']; ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?= $studentId ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $studentId ?>" aria-expanded="false" aria-controls="collapse<?= $studentId ?>">
                                    <div class="d-flex justify-content-between w-100 align-items-center">
                                        <div>
                                            <strong><?= htmlspecialchars($student['firstname']) ?> <?= htmlspecialchars($student['lastname']) ?></strong>
                                            <span class="text-muted ms-2"><?= htmlspecialchars($student['email']) ?></span>
                                        </div>
                                        <span class="badge bg-primary rounded-pill"><?= count($wishlistData['items']) ?> offre(s)</span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse<?= $studentId ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $studentId ?>" data-bs-parent="#wishlistAccordion">
                                <div class="accordion-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Offre</th>
                                                    <th>Ajoutée le</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($wishlistData['items'] as $item): ?>
                                                    <tr>
                                                        <td>
                                                            <a href="index.php?page=offers&action=view&id=<?= $item['offer_id'] ?>" class="text-decoration-none">
                                                                <?= htmlspecialchars($item['offer_details']['title']) ?>
                                                            </a>
                                                        </td>
                                                        <td><?= date('d/m/Y', strtotime($item['created_at'])) ?></td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm" role="group">
                                                                <a href="index.php?page=offers&action=view&id=<?= $item['offer_id'] ?>" class="btn btn-outline-primary" title="Voir l'offre">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="index.php?page=wishlist&action=remove&id=<?= $item['offer_id'] ?>&student_id=<?= $studentId ?>" class="btn btn-outline-danger" title="Retirer de la wishlist" onclick="return confirm('Êtes-vous sûr de vouloir retirer cette offre de la wishlist de l\'étudiant?');">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-end mt-2">
                                        <a href="index.php?page=wishlist&student_id=<?= $studentId ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-external-link-alt"></i> Voir la wishlist complète
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../Templates/footer.php'; ?>
