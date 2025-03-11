    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Gestion des stages</h5>
                    <p>Une plateforme complète pour la gestion des offres de stage entre entreprises et étudiants</p>
                </div>
                <div class="col-md-3">
                    <h5>Liens utiles</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white">Accueil</a></li>
                        <li><a href="index.php?page=companies" class="text-white">Entreprises</a></li>
                        <li><a href="index.php?page=offers" class="text-white">Offres de stage</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contact</h5>
                    <address>
                        <i class="fas fa-map-marker-alt me-2"></i> 123 Avenue de l'Université<br>
                        <i class="fas fa-phone me-2"></i> +33 1 23 45 67 89<br>
                        <i class="fas fa-envelope me-2"></i> <a href="mailto:contact@stages.fr" class="text-white">contact@stages.fr</a>
                    </address>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?= date('Y') ?> Gestion des stages. Tous droits réservés.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item"><a href="index.php?page=pages&action=legal" class="text-white">Mentions légales</a></li>
                        <li class="list-inline-item"><a href="index.php?page=pages&action=privacy" class="text-white">Politique de confidentialité</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Custom JS -->
    <script src="Public/js/script.js"></script>
    
    <!-- Activation des tooltips et popovers Bootstrap -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Activate tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Activate popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
            
            // Flash messages auto-close
            setTimeout(function() {
                var alertList = document.querySelectorAll('.alert-dismissible');
                alertList.forEach(function (alert) {
                    new bootstrap.Alert(alert).close();
                });
            }, 5000);
        });
    </script>
</body>
</html>
