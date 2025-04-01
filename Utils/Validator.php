<?php
namespace Utils;

/**
 * Classe utilitaire pour valider les données de formulaire
 * Permet de définir des règles de validation et de vérifier si les données sont valides
 */
class Validator 
{
    /**
     * Stocke les erreurs de validation
     * @var array
     */
    private $errors = [];
    
    /**
     * Valide une valeur selon des règles spécifiées
     * @param mixed $value Valeur à valider
     * @param string $fieldName Nom du champ (pour les messages d'erreur)
     * @param string $rules Règles de validation séparées par des pipes (|)
     * @return bool True si la validation réussit, false sinon
     */
    public function validate($value, $fieldName, $rules)
    {
        // Séparation des règles
        $ruleArray = explode('|', $rules);
        
        // Application de chaque règle
        foreach ($ruleArray as $rule) {
            // Vérification si la règle a des paramètres
            if (strpos($rule, ':') !== false) {
                list($ruleName, $ruleParam) = explode(':', $rule, 2);
            } else {
                $ruleName = $rule;
                $ruleParam = null;
            }
            
            // Application de la règle
            switch ($ruleName) {
                case 'required':
                    if (empty($value) && $value !== '0') {
                        $this->errors[] = "Le champ {$fieldName} est requis.";
                        return false;
                    }
                    break;
                    
                case 'email':
                    if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->errors[] = "Le champ {$fieldName} doit être une adresse email valide.";
                        return false;
                    }
                    break;
                    
                case 'min':
                    if (!empty($value) && strlen($value) < $ruleParam) {
                        $this->errors[] = "Le champ {$fieldName} doit contenir au moins {$ruleParam} caractères.";
                        return false;
                    }
                    break;
                    
                case 'max':
                    if (!empty($value) && strlen($value) > $ruleParam) {
                        $this->errors[] = "Le champ {$fieldName} ne doit pas dépasser {$ruleParam} caractères.";
                        return false;
                    }
                    break;
                    
                case 'alpha':
                    if (!empty($value) && !preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ\-\' ]+$/', $value)) {
                        $this->errors[] = "Le champ {$fieldName} doit contenir uniquement des lettres, espaces, tirets et apostrophes.";
                        return false;
                    }
                    break;
                    
                case 'alphanumeric':
                    if (!empty($value) && !preg_match('/^[A-Za-z0-9À-ÖØ-öø-ÿ\-\' ]+$/', $value)) {
                        $this->errors[] = "Le champ {$fieldName} doit contenir uniquement des lettres, chiffres, espaces, tirets et apostrophes.";
                        return false;
                    }
                    break;
                    
                case 'numeric':
                    if (!empty($value) && !is_numeric($value)) {
                        $this->errors[] = "Le champ {$fieldName} doit être un nombre.";
                        return false;
                    }
                    break;
                    
                case 'integer':
                    if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
                        $this->errors[] = "Le champ {$fieldName} doit être un nombre entier.";
                        return false;
                    }
                    break;
                    
                case 'float':
                    if (!empty($value) && !filter_var($value, FILTER_VALIDATE_FLOAT)) {
                        $this->errors[] = "Le champ {$fieldName} doit être un nombre décimal.";
                        return false;
                    }
                    break;
                    
                case 'date':
                    if (!empty($value)) {
                        $date = \DateTime::createFromFormat('Y-m-d', $value);
                        if (!$date || $date->format('Y-m-d') !== $value) {
                            $this->errors[] = "Le champ {$fieldName} doit être une date valide (YYYY-MM-DD).";
                            return false;
                        }
                    }
                    break;
                    
                case 'url':
                    if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                        $this->errors[] = "Le champ {$fieldName} doit être une URL valide.";
                        return false;
                    }
                    break;
            }
        }
        
        return true;
    }
    
    /**
     * Vérifie si la validation a réussi
     * @return bool True si aucune erreur, false sinon
     */
    public function isValid()
    {
        return empty($this->errors);
    }
    
    /**
     * Récupère toutes les erreurs de validation
     * @return array Tableau des erreurs
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * Récupère la première erreur de validation
     * @return string|null Première erreur ou null s'il n'y en a pas
     */
    public function getFirstError()
    {
        return !empty($this->errors) ? $this->errors[0] : null;
    }
    
    /**
     * Réinitialise les erreurs de validation
     * @return void
     */
    public function resetErrors()
    {
        $this->errors = [];
    }
}
