<?php include 'views/header.php'; ?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-8">
            <h1>Gestion des Rôles</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="index.php?controller=role&action=create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter un rôle
            </a>
        </div>
    </div>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Permissions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($roles)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Aucun rôle trouvé</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($roles as $role): ?>
                                <tr>
                                    <td><?php echo $role->id; ?></td>
                                    <td><?php echo htmlspecialchars($role->nom); ?></td>
                                    <td><?php echo htmlspecialchars($role->description ?? ''); ?></td>
                                    <td>
                                        <?php 
                                        $permissions = $this->role->getRolePermissions($role->id);
                                        $permCount = count($permissions);
                                        if ($permCount > 0) {
                                            echo '<span class="badge bg-info">' . $permCount . ' permission' . ($permCount > 1 ? 's' : '') . '</span>'; 
                                        } else {
                                            echo '<span class="badge bg-warning">Aucune permission</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="index.php?controller=role&action=edit&id=<?php echo $role->id; ?>" 
                                               class="btn btn-sm btn-outline-secondary"
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if (!$this->role->roleHasUsers($role->id)): ?>
                                                <a href="#" 
                                                   onclick="confirmDelete(<?php echo $role->id; ?>, '<?php echo addslashes(htmlspecialchars($role->nom)); ?>')" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer le rôle <strong id="roleName"></strong> ?
                <p class="text-danger mt-2 mb-0">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="#" id="deleteLink" class="btn btn-danger">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id, name) {
        document.getElementById('roleName').textContent = name;
        document.getElementById('deleteLink').href = 'index.php?controller=role&action=delete&id=' + id;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        modal.show();
    }
</script>

<?php include 'views/footer.php'; ?>
