<?php
namespace Controllers;

class PageController 
{
    /**
     * Affiche la page des mentions légales
     */
    public function legal() 
    {
        $pageTitle = 'Mentions légales';
        require_once __DIR__ . '/../Views/Page/legal.php';
    }
    
    /**
     * Affiche la page de politique de confidentialité
     */
    public function privacy() 
    {
        $pageTitle = 'Politique de confidentialité';
        require_once __DIR__ . '/../Views/Page/privacy.php';
    }
}
