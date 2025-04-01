<?php include_once __DIR__ . '/../Templates/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Administration des candidatures</h1>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3><?= $stats['total'] ?></h3>
                    <p class="mb-0">Total</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3><?= $stats['pending'] ?></h3>
                    <p class="mb-0">En attente</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3><?= $stats['accepted'] ?></h3>
                    <p class="mb-0">Acceptées</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h3><?= $stats['rejected'] ?></h3>
                    <p class="mb-0">Refusées</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-list"></i> Liste des candidatures</h5>
        </div>
        <div class="card-body">
            <?php if (empty($applications)): ?>
                <div class="alert alert-info">
                    Aucune candidature trouvée.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Étudiant</th>
                                <th>Offre</th>
                                <th>Entreprise</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $application): ?>
                                <tr>
                                    <td>
                                        <a href="index.php?page=applications&student_id=<?= $application['student_id'] ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($application['firstname']) ?> <?= htmlspecialchars($application['lastname']) ?>
                                        </a>
                                        <div class="text-muted small"><?= htmlspecialchars($application['email']) ?></div>
                                    </td>
                                    <td>
                                        <a href="index.php?page=offers&action=view&id=<?= $application['offer_id'] ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($application['offer_title']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($application['company_name']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($application['created_at'])) ?></td>
                                    <td>
                                        <?php 
                                        $statusClass = [
                                            'pending' => 'bg-warning',
                                            'accepted' => 'bg-success',
                                            'rejected' => 'bg-danger'
                                        ][$application['status']] ?? 'bg-secondary';
                                        
                                        $statusLabel = [
                                            'pending' => 'En attente',
                                            'accepted' => 'Acceptée',
                                            'rejected' => 'Refusée'
                                        ][$application['status']] ?? 'Inconnu';
                                        ?>
                                        <span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="index.php?page=applications&action=view&id=<?= $application['id'] ?>" class="btn btn-outline-primary" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($application['status'] === 'pending'): ?>
                                                <a href="index.php?page=applications&action=update&id=<?= $application['id'] ?>&status=accepted" class="btn btn-outline-success" title="Accepter">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                                <a href="index.php?page=applications&action=update&id=<?= $application['id'] ?>&status=rejected" class="btn btn-outline-danger" title="Refuser">
                                                    <i class="fas fa-times"></i>
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
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../Templates/footer.php'; ?>
