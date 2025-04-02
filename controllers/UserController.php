<?php
namespace Controllers;

use Models\User;
use Models\Auth;

class UserController {
    private $userModel;
    private $auth;
    
    public function __construct() {
        $this->userModel = new \Models\User();
        $this->auth = \Models\Auth::getInstance();
    }
    
    // Affiche la page de profil de l'utilisateur connecté
    public function profile() {
        $userId = $this->auth->getUserId();
        $user = $this->userModel->find($userId);
        
        // Récupération de la liste des étudiants si l'utilisateur a les droits
        $students = [];
        $canViewStudents = $this->auth->hasPermission('view_students');
        $canEditUsers = $this->auth->hasPermission('edit_users');
        
        if ($canViewStudents) {
            $studentModel = new \Models\Student();
            $students = $studentModel->getAll();
        }
        
        include 'views/profile.php';
    }
    
    // Affiche la liste de tous les utilisateurs
    public function index() {
        // Vérification des permissions
        if (!$this->auth->hasPermission('view_users')) {
            $_SESSION['error'] = "Vous n'avez pas les droits nécessaires pour accéder à cette page.";
            header('Location: index.php');
            exit();
        }
        
        $users = $this->userModel->getAllUsers();
        include 'views/User/index.php';
    }
    
    // Affiche les détails d'un utilisateur spécifique
    public function view($id = null) {
        // Vérification des permissions
        if (!$this->auth->hasPermission('view_users')) {
            $_SESSION['error'] = "Vous n'avez pas les droits nécessaires pour accéder à cette page.";
            header('Location: index.php');
            exit();
        }
        
        // Si aucun ID n'est fourni, utiliser l'ID de l'utilisateur connecté
        if ($id === null) {
            $id = $this->auth->getUserId();
        }
        
        $user = $this->userModel->getUserById($id);
        
        if (!$user) {
            $_SESSION['error'] = "Utilisateur introuvable.";
            header('Location: index.php?page=users');
            exit();
        }
        
        include 'views/User/view.php';
    }
    
    // Affiche et traite le formulaire d'édition du profil
    public function editProfile($id = null) {
        // Si aucun ID n'est fourni, utiliser l'ID de l'utilisateur connecté
        if ($id === null) {
            $id = $this->auth->getUserId();
        } else {
            // Si un ID est fourni, vérifier les permissions
            if (!$this->auth->hasPermission('edit_users') && $id != $this->auth->getUserId()) {
                $_SESSION['error'] = "Vous n'avez pas les droits nécessaires pour modifier cet utilisateur.";
                header('Location: index.php');
                exit();
            }
        }
        
        $user = $this->userModel->getUserById($id);
        
        if (!$user) {
            $_SESSION['error'] = "Utilisateur introuvable.";
            header('Location: index.php?page=users');
            exit();
        }
        
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telephone = trim($_POST['telephone'] ?? '');
            $centre = trim($_POST['centre'] ?? '');
            $promotion = trim($_POST['promotion'] ?? '');
            
            // Validation
            if (empty($nom)) {
                $errors['nom'] = "Le nom est requis.";
            }
            
            if (empty($prenom)) {
                $errors['prenom'] = "Le prénom est requis.";
            }
            
            if (empty($email)) {
                $errors['email'] = "L'email est requis.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "L'email n'est pas valide.";
            } elseif ($email !== $user['email'] && $this->userModel->emailExists($email)) {
                $errors['email'] = "Cet email est déjà utilisé.";
            }
            
            // Si pas d'erreur, mettre à jour les données
            if (empty($errors)) {
                $userData = [
                    'id' => $id,
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email,
                    'telephone' => $telephone,
                    'centre' => $centre,
                    'promotion' => $promotion
                ];
                
                if ($this->userModel->updateUser($userData)) {
                    $_SESSION['success'] = "Profil mis à jour avec succès.";
                    header('Location: index.php?page=user&action=view&id=' . $id);
                    exit();
                } else {
                    $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour du profil.";
                }
            }
        }
        
        include 'views/User/edit_profile.php';
    }
    
    // Affiche et traite le formulaire de changement de mot de passe
    public function changePassword($id = null) {
        // Si aucun ID n'est fourni, utiliser l'ID de l'utilisateur connecté
        if ($id === null) {
            $id = $this->auth->getUserId();
        } else {
            // Si un ID est fourni, vérifier les permissions
            if (!$this->auth->hasPermission('edit_users') && $id != $this->auth->getUserId()) {
                $_SESSION['error'] = "Vous n'avez pas les droits nécessaires pour modifier cet utilisateur.";
                header('Location: index.php');
                exit();
            }
        }
        
        // Utiliser findWithPassword pour avoir accès au mot de passe hashé
        $user = $this->userModel->findWithPassword($id);
        
        if (!$user) {
            $_SESSION['error'] = "Utilisateur introuvable.";
            header('Location: index.php?page=users');
            exit();
        }
        
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Si l'utilisateur modifie son propre mot de passe, vérifier l'ancien
            if ($id == $this->auth->getUserId()) {
                if (empty($currentPassword)) {
                    $errors['current_password'] = "Le mot de passe actuel est requis.";
                } elseif (!password_verify($currentPassword, $user['password'])) {
                    $errors['current_password'] = "Le mot de passe actuel est incorrect.";
                }
            }
            
            // Validation du nouveau mot de passe
            if (empty($newPassword)) {
                $errors['new_password'] = "Le nouveau mot de passe est requis.";
            } elseif (strlen($newPassword) < 8) {
                $errors['new_password'] = "Le nouveau mot de passe doit contenir au moins 8 caractères.";
            }
            
            if ($newPassword !== $confirmPassword) {
                $errors['confirm_password'] = "Les mots de passe ne correspondent pas.";
            }
            
            // Si pas d'erreur, mettre à jour le mot de passe
            if (empty($errors)) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                
                if ($this->userModel->updatePassword($id, $hashedPassword)) {
                    $_SESSION['success'] = "Mot de passe mis à jour avec succès.";
                    header('Location: index.php?page=profile');
                    exit();
                } else {
                    $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour du mot de passe.";
                }
            }
        }
        
        // Stocker les données pour la vue
        $_SESSION['user_id'] = $this->auth->getUserId();
        
        include 'views/User/change_password.php';
    }
    
    // Affiche et traite le formulaire de création d'un nouvel utilisateur
    public function create() {
        // Vérification des permissions
        if (!$this->auth->hasPermission('create_users')) {
            $_SESSION['error'] = "Vous n'avez pas les droits nécessaires pour créer un utilisateur.";
            header('Location: index.php');
            exit();
        }
        
        $roles = $this->userModel->getAllRoles();
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $role = $_POST['role'] ?? '';
            $telephone = trim($_POST['telephone'] ?? '');
            $centre = trim($_POST['centre'] ?? '');
            $promotion = trim($_POST['promotion'] ?? '');
            
            // Validation
            if (empty($nom)) {
                $errors['nom'] = "Le nom est requis.";
            }
            
            if (empty($prenom)) {
                $errors['prenom'] = "Le prénom est requis.";
            }
            
            if (empty($email)) {
                $errors['email'] = "L'email est requis.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "L'email n'est pas valide.";
            } elseif ($this->userModel->emailExists($email)) {
                $errors['email'] = "Cet email est déjà utilisé.";
            }
            
            if (empty($password)) {
                $errors['password'] = "Le mot de passe est requis.";
            } elseif (strlen($password) < 8) {
                $errors['password'] = "Le mot de passe doit contenir au moins 8 caractères.";
            }
            
            if ($password !== $confirmPassword) {
                $errors['confirm_password'] = "Les mots de passe ne correspondent pas.";
            }
            
            if (empty($role)) {
                $errors['role'] = "Le rôle est requis.";
            }
            
            // Si pas d'erreur, créer l'utilisateur
            if (empty($errors)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $userData = [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email,
                    'password' => $hashedPassword,
                    'role' => $role,
                    'telephone' => $telephone,
                    'centre' => $centre,
                    'promotion' => $promotion
                ];
                
                if ($userId = $this->userModel->createUser($userData)) {
                    $_SESSION['success'] = "Utilisateur créé avec succès.";
                    header('Location: index.php?page=user&action=view&id=' . $userId);
                    exit();
                } else {
                    $_SESSION['error'] = "Une erreur est survenue lors de la création de l'utilisateur.";
                }
            }
        }
        
        include 'views/User/create.php';
    }
    
    // Supprime un utilisateur
    public function delete($id) {
        // Vérification des permissions
        if (!$this->auth->hasPermission('delete_users')) {
            $_SESSION['error'] = "Vous n'avez pas les droits nécessaires pour supprimer un utilisateur.";
            header('Location: index.php');
            exit();
        }
        
        // Empêcher la suppression de son propre compte
        if ($id == $this->auth->getUserId()) {
            $_SESSION['error'] = "Vous ne pouvez pas supprimer votre propre compte.";
            header('Location: index.php?page=users');
            exit();
        }
        
        $user = $this->userModel->getUserById($id);
        
        if (!$user) {
            $_SESSION['error'] = "Utilisateur introuvable.";
            header('Location: index.php?page=users');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
            if ($this->userModel->deleteUser($id)) {
                $_SESSION['success'] = "Utilisateur supprimé avec succès.";
                header('Location: index.php?page=users');
                exit();
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de la suppression de l'utilisateur.";
                header('Location: index.php?page=user&action=view&id=' . $id);
                exit();
            }
        }
        
        include 'views/User/delete.php';
    }
}
?>