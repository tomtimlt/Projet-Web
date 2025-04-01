<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une entreprise</title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Ajouter une entreprise</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="index.php?action=entreprises">Retour u00e0 la liste</a></li>
                </ul>
            </nav>
        </header>
        
        <?php 
        // Ru00e9cupu00e9ration des donnu00e9es du formulaire en cas d'erreur
        $formData = $_SESSION['form_data'] ?? [];
        unset($_SESSION['form_data']);
        ?>
        
        <?php if (isset($_SESSION['form_errors'])): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($_SESSION['form_errors'] as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['form_errors']); ?>
        <?php endif; ?>
        
        <div class="form-container">
            <form action="index.php?action=store_entreprise" method="post" enctype="multipart/form-data" id="entrepriseForm">
                <div class="form-section">
                    <h3>Informations gu00e9nu00e9rales</h3>
                    
                    <div class="form-group">
                        <label for="nom">Nom de l'entreprise<span class="required">*</span></label>
                        <input type="text" id="nom" name="nom" value="<?php echo isset($formData['nom']) ? htmlspecialchars($formData['nom']) : ''; ?>" required>
                        <div class="error-message" id="nom-error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="secteur_activite">Secteur d'activitu00e9<span class="required">*</span></label>
                        <input type="text" id="secteur_activite" name="secteur_activite" value="<?php echo isset($formData['secteur_activite']) ? htmlspecialchars($formData['secteur_activite']) : ''; ?>" required>
                        <div class="error-message" id="secteur-error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4"><?php echo isset($formData['description']) ? htmlspecialchars($formData['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="logo">Logo</label>
                        <input type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/gif">
                        <small>Formats acceptu00e9s: JPG, PNG, GIF. Taille max: 2 Mo.</small>
                        <div class="error-message" id="logo-error"></div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3>Adresse</h3>
                    
                    <div class="form-group">
                        <label for="adresse">Adresse<span class="required">*</span></label>
                        <input type="text" id="adresse" name="adresse" value="<?php echo isset($formData['adresse']) ? htmlspecialchars($formData['adresse']) : ''; ?>" required>
                        <div class="error-message" id="adresse-error"></div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="ville">Ville<span class="required">*</span></label>
                            <input type="text" id="ville" name="ville" value="<?php echo isset($formData['ville']) ? htmlspecialchars($formData['ville']) : ''; ?>" required>
                            <div class="error-message" id="ville-error"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="code_postal">Code postal<span class="required">*</span></label>
                            <input type="text" id="code_postal" name="code_postal" value="<?php echo isset($formData['code_postal']) ? htmlspecialchars($formData['code_postal']) : ''; ?>" required>
                            <div class="error-message" id="code-postal-error"></div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="pays">Pays<span class="required">*</span></label>
                        <input type="text" id="pays" name="pays" value="<?php echo isset($formData['pays']) ? htmlspecialchars($formData['pays']) : ''; ?>" required>
                        <div class="error-message" id="pays-error"></div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3>Contact</h3>
                    
                    <div class="form-group">
                        <label for="telephone">Tu00e9lu00e9phone</label>
                        <input type="tel" id="telephone" name="telephone" value="<?php echo isset($formData['telephone']) ? htmlspecialchars($formData['telephone']) : ''; ?>">
                        <div class="error-message" id="telephone-error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo isset($formData['email']) ? htmlspecialchars($formData['email']) : ''; ?>">
                        <div class="error-message" id="email-error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="site_web">Site web</label>
                        <input type="url" id="site_web" name="site_web" value="<?php echo isset($formData['site_web']) ? htmlspecialchars($formData['site_web']) : ''; ?>">
                        <div class="error-message" id="site-web-error"></div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-submit">Enregistrer</button>
                    <a href="index.php?action=entreprises" class="btn-cancel">Annuler</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="assets/js/entreprise-form.js"></script>
</body>
</html>
