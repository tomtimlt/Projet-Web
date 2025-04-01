<?php
$pageTitle = 'Tableau de bord';
require_once 'views/templates/header.php';
?>

<div class="dashboard">
    <div class="welcome-section">
        <h2>Bienvenue sur le système de gestion d'entreprises</h2>
        <p>Cette plateforme vous permet de gérer des informations sur les entreprises, de les évaluer et de consulter des statistiques.</p>
    </div>
    
    <div class="dashboard-cards">
        <div class="dashboard-card">
            <h3>Entreprises</h3>
            <p>Consultez, ajoutez et gérez des informations sur les entreprises</p>
            <a href="index.php?action=entreprises" class="btn-view">Voir les entreprises</a>
        </div>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="dashboard-card">
                <h3>Mes évaluations</h3>
                <p>Consultez et gérez vos évaluations d'entreprises</p>
                <a href="index.php?action=my_evaluations" class="btn-view">Voir mes évaluations</a>
            </div>
        <?php endif; ?>
        
        <div class="dashboard-card">
            <h3>Statistiques</h3>
            <p>Consultez les statistiques globales sur les entreprises</p>
            <a href="index.php?action=statistics" class="btn-view">Voir les statistiques</a>
        </div>
    </div>
    
    <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="auth-prompt">
            <p>Pour profiter de toutes les fonctionnalités, veuillez vous connecter ou créer un compte.</p>
            <div class="auth-buttons">
                <a href="index.php?action=login" class="btn-create">Se connecter</a>
                <a href="index.php?action=register_form" class="btn-view">S'inscrire</a>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="recent-activity">
        <h3>Entreprises récemment ajoutées</h3>
        
        <?php if (isset($this->recentEntreprises) && count($this->recentEntreprises) > 0): ?>
            <div class="entreprises-grid">
                <?php foreach ($this->recentEntreprises as $entreprise): ?>
                    <div class="entreprise-card">
                        <div class="entreprise-logo">
                            <?php if (!empty($entreprise->logo)): ?>
                                <img src="<?php echo htmlspecialchars($entreprise->logo); ?>" alt="Logo <?php echo htmlspecialchars($entreprise->nom); ?>">
                            <?php else: ?>
                                <div class="no-logo">Pas de logo</div>
                            <?php endif; ?>
                        </div>
                        <div class="entreprise-info">
                            <h3><?php echo htmlspecialchars($entreprise->nom); ?></h3>
                            <div class="secteur">
                                <span class="icon-briefcase"></span> <?php echo htmlspecialchars($entreprise->secteur_activite); ?>
                            </div>
                            <div class="location">
                                <span class="icon-location"></span> <?php echo htmlspecialchars($entreprise->ville); ?>, <?php echo htmlspecialchars($entreprise->pays); ?>
                            </div>
                            <?php if (isset($entreprise->note_moyenne)): ?>
                                <div class="rating">
                                    <div class="stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="star <?php echo $i <= round($entreprise->note_moyenne) ? 'filled' : ''; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="average">(<?php echo number_format($entreprise->note_moyenne, 1); ?>)</span>
                                </div>
                            <?php endif; ?>
                            <a href="index.php?action=view_entreprise&id=<?php echo $entreprise->id; ?>" class="btn-view">Voir détails</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="no-results">Aucune entreprise n'a été ajoutée récemment.</p>
        <?php endif; ?>
    </div>
</div>

<style>
    .dashboard {
        margin-bottom: 40px;
    }
    
    .welcome-section {
        background-color: white;
        padding: 30px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
        text-align: center;
    }
    
    .welcome-section h2 {
        color: #2c3e50;
        margin-bottom: 15px;
    }
    
    .welcome-section p {
        font-size: 16px;
        line-height: 1.6;
        color: #555;
    }
    
    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .dashboard-card {
        background-color: white;
        padding: 25px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: transform 0.3s;
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
    }
    
    .dashboard-card h3 {
        color: #2c3e50;
        margin-bottom: 15px;
        font-size: 20px;
    }
    
    .dashboard-card p {
        margin-bottom: 20px;
        color: #555;
    }
    
    .auth-prompt {
        background-color: #f9f9f9;
        padding: 25px;
        border-radius: 5px;
        margin-bottom: 30px;
        text-align: center;
    }
    
    .auth-prompt p {
        margin-bottom: 15px;
        font-size: 16px;
    }
    
    .auth-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
    }
    
    .recent-activity {
        background-color: white;
        padding: 30px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    
    .recent-activity h3 {
        color: #2c3e50;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    @media (max-width: 768px) {
        .dashboard-cards {
            grid-template-columns: 1fr;
        }
        
        .auth-buttons {
            flex-direction: column;
        }
        
        .auth-buttons a {
            width: 100%;
            margin-bottom: 10px;
        }
    }
</style>

<?php require_once 'views/templates/footer.php'; ?>
