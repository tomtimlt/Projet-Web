<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($this->entrepriseModel->nom); ?> - Détails</title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><?php echo htmlspecialchars($this->entrepriseModel->nom); ?></h1>
            <nav>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="index.php?action=entreprises">Retour à la liste</a></li>
                    <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'pilote')): ?>
                        <li><a href="index.php?action=edit_entreprise&id=<?php echo $this->entrepriseModel->id; ?>" class="btn-edit">Modifier</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <li>
                            <form action="index.php?action=delete_entreprise&id=<?php echo $this->entrepriseModel->id; ?>" method="post" class="delete-form" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette entreprise ?');">
                                <button type="submit" class="btn-delete">Supprimer</button>
                            </form>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </header>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo $_SESSION['success_message']; 
                    unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo $_SESSION['error_message'];
                    unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>
        
        <div class="entreprise-details">
            <section class="main-info">
                <div class="logo-container">
                    <?php if (!empty($this->entrepriseModel->logo)): ?>
                        <img src="<?php echo htmlspecialchars($this->entrepriseModel->logo); ?>" alt="Logo <?php echo htmlspecialchars($this->entrepriseModel->nom); ?>" class="company-logo">
                    <?php else: ?>
                        <div class="no-logo large">Pas de logo</div>
                    <?php endif; ?>
                </div>
                
                <div class="info-container">
                    <div class="info-group">
                        <h3>Informations générales</h3>
                        <p><strong>Secteur d'activité:</strong> <?php echo htmlspecialchars($this->entrepriseModel->secteur_activite); ?></p>
                        <p><strong>Adresse:</strong> <?php echo htmlspecialchars($this->entrepriseModel->adresse); ?></p>
                        <p><strong>Ville:</strong> <?php echo htmlspecialchars($this->entrepriseModel->ville); ?></p>
                        <p><strong>Code postal:</strong> <?php echo htmlspecialchars($this->entrepriseModel->code_postal); ?></p>
                        <p><strong>Pays:</strong> <?php echo htmlspecialchars($this->entrepriseModel->pays); ?></p>
                    </div>
                    
                    <div class="info-group">
                        <h3>Contact</h3>
                        <?php if (!empty($this->entrepriseModel->telephone)): ?>
                            <p><strong>Téléphone:</strong> <?php echo htmlspecialchars($this->entrepriseModel->telephone); ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($this->entrepriseModel->email)): ?>
                            <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($this->entrepriseModel->email); ?>"><?php echo htmlspecialchars($this->entrepriseModel->email); ?></a></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($this->entrepriseModel->site_web)): ?>
                            <p><strong>Site web:</strong> <a href="<?php echo htmlspecialchars($this->entrepriseModel->site_web); ?>" target="_blank"><?php echo htmlspecialchars($this->entrepriseModel->site_web); ?></a></p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
            
            <?php if (!empty($this->entrepriseModel->description)): ?>
                <section class="description">
                    <h3>Description</h3>
                    <div class="description-content">
                        <?php echo nl2br(htmlspecialchars($this->entrepriseModel->description)); ?>
                    </div>
                </section>
            <?php endif; ?>
            
            <section class="statistics">
                <h3>Statistiques</h3>
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="icon-briefcase"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $stats['nombre_offres']; ?></div>
                            <div class="stat-label">Offres</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon"><i class="icon-star"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo round($stats['note_moyenne'], 1); ?></div>
                            <div class="stat-label">Note moyenne</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon"><i class="icon-comment"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $stats['nombre_evaluations']; ?></div>
                            <div class="stat-label">Évaluations</div>
                        </div>
                    </div>
                </div>
            </section>
            
            <section class="evaluations">
                <h3>Évaluations</h3>
                
                <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['pilote', 'etudiant'])): ?>
                    <div class="evaluation-form">
                        <h4>Donnez votre avis</h4>
                        
                        <?php if (isset($_SESSION['evaluation_errors'])): ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php foreach ($_SESSION['evaluation_errors'] as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                    <?php unset($_SESSION['evaluation_errors']); ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form action="index.php?action=evaluate_entreprise&id=<?php echo $this->entrepriseModel->id; ?>" method="post">
                            <div class="form-group">
                                <label for="note">Note</label>
                                <div class="rating-select">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <input type="radio" name="note" id="star<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo (isset($userEvaluation) && $userEvaluation['note'] == $i) ? 'checked' : ''; ?>>
                                        <label for="star<?php echo $i; ?>" title="<?php echo $i; ?> étoile(s)"><?php echo $i; ?></label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="commentaire">Commentaire (optionnel)</label>
                                <textarea id="commentaire" name="commentaire" rows="4"><?php echo isset($userEvaluation) ? htmlspecialchars($userEvaluation['commentaire']) : ''; ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn-submit"><?php echo isset($userEvaluation) ? 'Modifier mon évaluation' : 'Soumettre mon évaluation'; ?></button>
                        </form>
                    </div>
                <?php endif; ?>
                
                <div class="evaluations-list">
                    <?php if (empty($evaluations)): ?>
                        <p class="no-evaluations">Aucune évaluation pour le moment.</p>
                    <?php else: ?>
                        <?php foreach ($evaluations as $evaluation): ?>
                            <div class="evaluation-card">
                                <div class="evaluation-header">
                                    <div class="user-info">
                                        <span class="user-name"><?php echo htmlspecialchars($evaluation['user_name']); ?></span>
                                        <span class="evaluation-date"><?php echo date('d/m/Y', strtotime($evaluation['date_evaluation'])); ?></span>
                                    </div>
                                    <div class="rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="star <?php echo ($i <= $evaluation['note']) ? 'filled' : ''; ?>"><?php echo ($i <= $evaluation['note']) ? '★' : '☆'; ?></span>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                
                                <?php if (!empty($evaluation['commentaire'])): ?>
                                    <div class="evaluation-content">
                                        <?php echo nl2br(htmlspecialchars($evaluation['commentaire'])); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
