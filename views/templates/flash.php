<?php
// Fichier d'affichage des messages flash
// Utilise la classe Utils\Flash

use Utils\Flash;

// Récupérer et afficher les messages flash
$flashMessages = Flash::getFlash();
if (!empty($flashMessages)) {
    foreach ($flashMessages as $type => $message) {
        $alertClass = 'alert-info';
        $icon = 'info-circle';
        
        // Définir la classe CSS et l'icône en fonction du type de message
        switch ($type) {
            case 'success':
                $alertClass = 'alert-success';
                $icon = 'check-circle';
                break;
            case 'error':
                $alertClass = 'alert-danger';
                $icon = 'exclamation-circle';
                break;
            case 'warning':
                $alertClass = 'alert-warning';
                $icon = 'exclamation-triangle';
                break;
        }
?>
        <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
            <i class="fas fa-<?= $icon ?> me-2"></i>
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php
    }
    // Vider les messages après affichage
    Flash::clearFlash();
}
?>
