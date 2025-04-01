<?php
namespace Utils;

/**
 * Classe utilitaire pour gérer les messages flash
 * Ces messages sont stockés en session et affichés au prochain chargement de page
 */
class Flash
{
    /**
     * Définit un message flash en session
     * @param string $type Le type de message (success, error, warning, info)
     * @param string $message Le contenu du message
     * @return void
     */
    public static function setFlash($type, $message)
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
    
    /**
     * Vérifie si un message flash existe en session
     * @return bool True si un message flash existe, false sinon
     */
    public static function hasFlash()
    {
        return isset($_SESSION['flash']);
    }
    
    /**
     * Récupère le message flash en session puis le supprime
     * @return array|null Le message flash ou null s'il n'y en a pas
     */
    public static function getFlash()
    {
        if (self::hasFlash()) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
    
    /**
     * Affiche le message flash s'il existe puis le supprime
     * @return void
     */
    public static function displayFlash()
    {
        if (self::hasFlash()) {
            $flash = self::getFlash();
            $type = $flash['type'];
            $message = $flash['message'];
            
            // Convertir le type en classe Bootstrap appropriée
            $class = match($type) {
                'success' => 'alert-success',
                'error' => 'alert-danger',
                'warning' => 'alert-warning',
                default => 'alert-info'
            };
            
            echo '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">';
            echo $message;
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
        }
    }
}
