<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des entreprises</title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Liste des entreprises</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'pilote')): ?>
                        <li><a href="index.php?action=create_entreprise" class="btn-create">Ajouter une entreprise</a></li>
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
        
        <section class="search-section">
            <h2>Rechercher une entreprise</h2>
            <form action="index.php" method="get" class="search-form">
                <input type="hidden" name="action" value="entreprises">
                <input type="hidden" name="search" value="1">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom">Nom</label>
                        <input type="text" id="nom" name="nom" value="<?php echo isset($_GET['nom']) ? htmlspecialchars($_GET['nom']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="secteur">Secteur d'activitu00e9</label>
                        <input type="text" id="secteur" name="secteur" value="<?php echo isset($_GET['secteur']) ? htmlspecialchars($_GET['secteur']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="ville">Ville</label>
                        <input type="text" id="ville" name="ville" value="<?php echo isset($_GET['ville']) ? htmlspecialchars($_GET['ville']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="pays">Pays</label>
                        <input type="text" id="pays" name="pays" value="<?php echo isset($_GET['pays']) ? htmlspecialchars($_GET['pays']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn-search">Rechercher</button>
                    <a href="index.php?action=entreprises" class="btn-reset">Ru00e9initialiser</a>
                </div>
            </form>
        </section>
        
        <section class="results-section">
            <h2><?php echo count($entreprises); ?> entreprise(s) trouvu00e9e(s)</h2>
            
            <?php if (empty($entreprises)): ?>
                <p class="no-results">Aucune entreprise trouvu00e9e. <a href="index.php?action=create_entreprise">Ajouter une entreprise</a>.</p>
            <?php else: ?>
                <div class="entreprises-grid">
                    <?php foreach ($entreprises as $entreprise): ?>
                        <div class="entreprise-card">
                            <div class="entreprise-logo">
                                <?php if (!empty($entreprise['logo'])): ?>
                                    <img src="<?php echo htmlspecialchars($entreprise['logo']); ?>" alt="Logo <?php echo htmlspecialchars($entreprise['nom']); ?>">
                                <?php else: ?>
                                    <div class="no-logo">Pas de logo</div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="entreprise-info">
                                <h3><?php echo htmlspecialchars($entreprise['nom']); ?></h3>
                                <p class="secteur"><?php echo htmlspecialchars($entreprise['secteur_activite']); ?></p>
                                <p class="location">
                                    <i class="icon-location"></i> 
                                    <?php echo htmlspecialchars($entreprise['ville']) . ', ' . htmlspecialchars($entreprise['pays']); ?>
                                </p>
                                
                                <?php if (isset($entreprise['nombre_offres'])): ?>
                                    <p class="offres">
                                        <i class="icon-briefcase"></i> 
                                        <?php echo $entreprise['nombre_offres']; ?> offre(s)
                                    </p>
                                <?php endif; ?>
                                
                                <?php if (isset($entreprise['note_moyenne'])): ?>
                                    <div class="rating">
                                        <div class="stars">
                                            <?php 
                                            $rating = round($entreprise['note_moyenne']);
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= $rating) {
                                                    echo '<span class="star filled">&#9733;</span>';
                                                } else {
                                                    echo '<span class="star">&#9734;</span>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        <span class="average"><?php echo round($entreprise['note_moyenne'], 1); ?>/5</span>
                                    </div>
                                <?php endif; ?>
                                
                                <a href="index.php?action=view_entreprise&id=<?php echo $entreprise['id']; ?>" class="btn-view">Voir du00e9tails</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
