<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'entreprise - <?php echo htmlspecialchars($this->entrepriseModel->nom); ?></title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Modifier l'entreprise</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="index.php?action=entreprises">Retour u00e0 la liste</a></li>
                    <li><a href="index.php?action=view_entreprise&id=<?php echo $this->entrepriseModel->id; ?>">Voir du00e9tails</a></li>
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
            <form action="index.php?action=update_entreprise&id=<?php echo $this->entrepriseModel->id; ?>" method="post" enctype="multipart/form-data" id="entrepriseForm">
                <div class="form-section">
                    <h3>Informations gu00e9nu00e9rales</h3>
                    
                    <div class="form-group">
                        <label for="nom">Nom de l'entreprise<span class="required">*</span></label>
                        <input type="text" id="nom" name="nom" value="<?php echo isset($formData['nom']) ? htmlspecialchars($formData['nom']) : htmlspecialchars($this->entrepriseModel->nom); ?>" required>
                        <div class="error-message" id="nom-error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="secteur_activite">Secteur d'activitu00e9<span class="required">*</span></label>
                        <input type="text" id="secteur_activite" name="secteur_activite" value="<?php echo isset($formData['secteur_activite']) ? htmlspecialchars($formData['secteur_activite']) : htmlspecialchars($this->entrepriseModel->secteur_activite); ?>" required>
                        <div class="error-message" id="secteur-error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4"><?php echo isset($formData['description']) ? htmlspecialchars($formData['description']) : htmlspecialchars($this->entrepriseModel->description); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="logo">Logo</label>
                        <?php if (!empty($this->entrepriseModel->logo)): ?>
                            <div class="current-logo">
                                <img src="<?php echo htmlspecialchars($this->entrepriseModel->logo); ?>" alt="Logo actuel" style="max-width: 100px;">
                                <p>Logo actuel</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/gif">
                        <small>Formats acceptu00e9s: JPG, PNG, GIF. Taille max: 2 Mo.</small>
                        <div class="error-message" id="logo-error"></div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3>Adresse</h3>
                    
                    <div class="form-group">
                        <label for="adresse">Adresse<span class="required">*</span></label>
                        <input type="text" id="adresse" name="adresse" value="<?php echo isset($formData['adresse']) ? htmlspecialchars($formData['adresse']) : htmlspecialchars($this->entrepriseModel->adresse); ?>" required>
                        <div class="error-message" id="adresse-error"></div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="ville">Ville<span class="required">*</span></label>
                            <input type="text" id="ville" name="ville" value="<?php echo isset($formData['ville']) ? htmlspecialchars($formData['ville']) : htmlspecialchars($this->entrepriseModel->ville); ?>" required>
                            <div class="error-message" id="ville-error"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="code_postal">Code postal<span class="required">*</span></label>
                            <input type="text" id="code_postal" name="code_postal" value="<?php echo isset($formData['code_postal']) ? htmlspecialchars($formData['code_postal']) : htmlspecialchars($this->entrepriseModel->code_postal); ?>" required>
                            <div class="error-message" id="code-postal-error"></div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="pays">Pays<span class="required">*</span></label>
                        <input type="text" id="pays" name="pays" value="<?php echo isset($formData['pays']) ? htmlspecialchars($formData['pays']) : htmlspecialchars($this->entrepriseModel->pays); ?>" required>
                        <div class="error-message" id="pays-error"></div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3>Contact</h3>
                    
                    <div class="form-group">
                        <label for="telephone">Tu00e9lu00e9phone</label>
                        <input type="tel" id="telephone" name="telephone" value="<?php echo isset($formData['telephone']) ? htmlspecialchars($formData['telephone']) : htmlspecialchars($this->entrepriseModel->telephone); ?>">
                        <div class="error-message" id="telephone-error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo isset($formData['email']) ? htmlspecialchars($formData['email']) : htmlspecialchars($this->entrepriseModel->email); ?>">
                        <div class="error-message" id="email-error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="site_web">Site web</label>
                        <input type="url" id="site_web" name="site_web" value="<?php echo isset($formData['site_web']) ? htmlspecialchars($formData['site_web']) : htmlspecialchars($this->entrepriseModel->site_web); ?>">
                        <div class="error-message" id="site-web-error"></div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-submit">Mettre u00e0 jour</button>
                    <a href="index.php?action=view_entreprise&id=<?php echo $this->entrepriseModel->id; ?>" class="btn-cancel">Annuler</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="assets/js/entreprise-form.js"></script>
</body>
</html>
