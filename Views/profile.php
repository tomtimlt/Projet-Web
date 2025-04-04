<!-- Views/profile.php -->
<?php require_once 'Templates/header.php'; ?>

<div class="container py-4">
    <!-- Messages de notification -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Informations Personnelles</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-placeholder mb-3">
                            <i class="fas fa-user-circle fa-5x text-secondary"></i>
                        </div>
                        <h3><?= htmlspecialchars($user['firstname'] ?? '') ?> <?= htmlspecialchars($user['lastname'] ?? '') ?></h3>
                        <span class="badge bg-info"><?= htmlspecialchars($user['role'] ?? '') ?></span>
                    </div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-envelope me-2 text-primary"></i>Email:</span>
                            <span class="text-muted"><?= htmlspecialchars($user['email'] ?? '') ?></span>
                        </li>
                        <?php if (!empty($user['telephone'])): ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span><i class="fas fa-phone me-2 text-primary"></i>Téléphone:</span>
                                <span class="text-muted"><?= htmlspecialchars($user['telephone']) ?></span>
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($user['centre'])): ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span><i class="fas fa-building me-2 text-primary"></i>Centre:</span>
                                <span class="text-muted"><?= htmlspecialchars($user['centre']) ?></span>
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($user['promotion'])): ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span><i class="fas fa-graduation-cap me-2 text-primary"></i>Promotion:</span>
                                <span class="text-muted"><?= htmlspecialchars($user['promotion']) ?></span>
                            </li>
                        <?php endif; ?>

                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-key me-2 text-primary"></i>Rôle:</span>
                            <span class="text-muted"><?= htmlspecialchars($user['role'] ?? '') ?></span>
                        </li>
                    </ul>

                    <div class="d-grid gap-2 mt-3">
                        <a href="index.php?page=edit_profile" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Modifier mon profil
                        </a>
                        <a href="index.php?page=password&action=change" class="btn btn-outline-secondary">
                            <i class="fas fa-lock me-2"></i>Changer mon mot de passe
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Espace Utilisateur</h4>
                </div>
                <div class="card-body">
                    <?php if ($user['role'] === 'etudiant'): ?>
                        <h5><i class="fas fa-user-graduate me-2 text-primary"></i>Espace Étudiant</h5>
                        <hr>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-clipboard-list me-2 text-primary"></i>Mes Candidatures</h5>
                                        <p class="card-text">Retrouvez et gérez toutes vos candidatures en cours.</p>
                                        <a href="index.php?page=applications" class="btn btn-sm btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-heart me-2 text-danger"></i>Ma Wishlist</h5>
                                        <p class="card-text">Consultez les offres de stage que vous avez enregistrées.</p>
                                        <a href="index.php?page=wishlist" class="btn btn-sm btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-briefcase me-2 text-primary"></i>Mon Stage</h5>
                                <p class="card-text">Consultez les informations relatives à votre stage actuel ou passé.</p>
                                <a href="index.php?page=my-internship" class="btn btn-sm btn-primary">Voir les détails</a>
                            </div>
                        </div>

                    <?php elseif ($user['role'] === 'pilote'): ?>
                        <h5><i class="fas fa-user-tie me-2 text-primary"></i>Espace Pilote</h5>
                        <hr>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-users me-2 text-primary"></i>Étudiants</h5>
                                        <p class="card-text">Gérez les étudiants.</p>
                                        <a href="index.php?page=students" class="btn btn-sm btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-building me-2 text-primary"></i>Entreprises</h5>
                                        <p class="card-text">Consultez et gérez les entreprises partenaires.</p>
                                        <a href="index.php?page=companies" class="btn btn-sm btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-briefcase me-2 text-primary"></i>Offres de Stage</h5>
                                        <p class="card-text">Gérez les offres de stage pour vos étudiants.</p>
                                        <a href="index.php?page=offers" class="btn btn-sm btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-chart-bar me-2 text-primary"></i>Statistiques</h5>
                                        <p class="card-text">Consultez les statistiques des offres.</p>
                                        <a href="index.php?page=offers&action=statistics" class="btn btn-sm btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php elseif ($user['role'] === 'admin'): ?>
                        <h5><i class="fas fa-user-shield me-2 text-primary"></i>Espace Administrateur</h5>
                        <hr>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-users me-2 text-primary"></i>Utilisateurs</h5>
                                        <p class="card-text">Gérez tous les utilisateurs du système.</p>
                                        <a href="index.php?page=students" class="btn btn-sm btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-building me-2 text-primary"></i>Entreprises</h5>
                                        <p class="card-text">Gérez toutes les entreprises partenaires.</p>
                                        <a href="index.php?page=companies" class="btn btn-sm btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-briefcase me-2 text-primary"></i>Offres de Stage</h5>
                                        <p class="card-text">Gérez toutes les offres de stage du système.</p>
                                        <a href="index.php?page=offers" class="btn btn-sm btn-primary">Accéder</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Liste des étudiants - Visible uniquement pour les rôles autorisés -->
            <?php if (!empty($students) && $canViewStudents): ?>
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Liste des Étudiants</h4>
                        <a href="index.php?page=students&action=export" class="btn btn-sm btn-light">
                            <i class="fas fa-download me-1"></i> Exporter
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="students-table">
                                <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Email</th>
                                    <th>Candidatures</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($student['lastname'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($student['firstname'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($student['email'] ?? '') ?></td>
                                        <td>
                                            <span class="badge bg-primary"><?= (int)($student['application_count'] ?? 0) ?></span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="index.php?page=student&action=view&id=<?= $student['id'] ?>" class="btn btn-outline-primary" title="Voir le profil">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($canEditUsers): ?>
                                                    <a href="index.php?page=student&action=edit&id=<?= $student['id'] ?>" class="btn btn-outline-secondary" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="index.php?page=password&action=change&id=<?= $student['id'] ?>" class="btn btn-outline-warning" title="Modifier le mot de passe">
                                                        <i class="fas fa-key"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Script pour l'interactivité du tableau des étudiants -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const studentsTable = document.getElementById('students-table');
        if (studentsTable) {
            // Fonction de recherche dans le tableau
            const searchInput = document.createElement('input');
            searchInput.classList.add('form-control', 'mb-3');
            searchInput.setAttribute('placeholder', 'Rechercher un étudiant...');
            searchInput.addEventListener('keyup', function() {
                const searchTerm = searchInput.value.toLowerCase();
                const rows = studentsTable.querySelectorAll('tbody tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });

            // Insérer le champ de recherche avant le tableau
            const tableContainer = studentsTable.parentNode;
            tableContainer.insertBefore(searchInput, studentsTable);
        }
    });
</script>

<?php require_once 'Templates/footer.php'; ?>
