/**
 * Module de pagination cote client
 * Permet de paginer du contenu HTML sans rechargement de page
 */
class Pagination {
    /**
     * Initialise le systeme de pagination
     * @param {Object} options - Options de configuration
     * @param {string} options.container - Selecteur CSS du conteneur des elements a paginer
     * @param {string} options.items - Selecteur CSS des elements a paginer
     * @param {number} options.itemsPerPage - Nombre d'elements par page
     * @param {string} options.paginationContainer - Selecteur CSS du conteneur de pagination
     * @param {Function} options.onPageChange - Fonction appelee lors du changement de page
     */
    constructor(options) {
        this.container = document.querySelector(options.container);
        this.itemsSelector = options.items;
        this.items = this.container ? this.container.querySelectorAll(options.items) : [];
        this.itemsPerPage = options.itemsPerPage || 10;
        this.paginationContainer = document.querySelector(options.paginationContainer);
        this.onPageChange = options.onPageChange || function() {};
        
        this.currentPage = 1;
        this.totalPages = Math.ceil(this.items.length / this.itemsPerPage);
        
        if (this.container && this.paginationContainer && this.items.length > 0) {
            this.init();
        }
    }
    
    /**
     * Initialise la pagination
     */
    init() {
        this.renderPagination();
        this.showPage(1);
    }
    
    /**
     * Affiche une page specifique
     * @param {number} pageNumber - Numero de page a afficher
     */
    showPage(pageNumber) {
        if (pageNumber < 1 || pageNumber > this.totalPages) {
            return;
        }
        
        this.currentPage = pageNumber;
        
        // Masquer tous les elements
        this.items.forEach(item => {
            item.style.display = 'none';
        });
        
        // Afficher les elements de la page courante
        const startIndex = (pageNumber - 1) * this.itemsPerPage;
        const endIndex = Math.min(startIndex + this.itemsPerPage, this.items.length);
        
        for (let i = startIndex; i < endIndex; i++) {
            this.items[i].style.display = '';
        }
        
        // Mettre a jour l'etat des boutons de pagination
        this.updatePaginationState();
        
        // Executer la fonction de callback
        this.onPageChange(pageNumber);
        
        // Scroll en haut du conteneur
        if (this.container) {
            window.scrollTo({
                top: this.container.offsetTop - 50,
                behavior: 'smooth'
            });
        }
    }
    
    /**
     * Genere les boutons de pagination
     */
    renderPagination() {
        if (!this.paginationContainer) return;
        
        this.paginationContainer.innerHTML = '';
        
        const paginationUl = document.createElement('ul');
        paginationUl.className = 'pagination-list';
        
        // Bouton precedent
        const prevButton = document.createElement('li');
        prevButton.className = 'pagination-item pagination-prev';
        prevButton.innerHTML = '&laquo; Precedent';
        prevButton.addEventListener('click', () => {
            if (this.currentPage > 1) {
                this.showPage(this.currentPage - 1);
            }
        });
        paginationUl.appendChild(prevButton);
        
        // Calculer les pages a afficher
        let startPage = Math.max(1, this.currentPage - 2);
        let endPage = Math.min(this.totalPages, startPage + 4);
        
        // Ajuster si necessaire pour toujours montrer 5 boutons quand possible
        if (endPage - startPage < 4 && this.totalPages > 5) {
            startPage = Math.max(1, endPage - 4);
        }
        
        // Premiere page
        if (startPage > 1) {
            const firstButton = document.createElement('li');
            firstButton.className = 'pagination-item';
            firstButton.textContent = '1';
            firstButton.addEventListener('click', () => this.showPage(1));
            paginationUl.appendChild(firstButton);
            
            if (startPage > 2) {
                const ellipsis = document.createElement('li');
                ellipsis.className = 'pagination-item pagination-ellipsis';
                ellipsis.textContent = '...';
                paginationUl.appendChild(ellipsis);
            }
        }
        
        // Pages numerotees
        for (let i = startPage; i <= endPage; i++) {
            const pageButton = document.createElement('li');
            pageButton.className = `pagination-item ${i === this.currentPage ? 'pagination-active' : ''}`;
            pageButton.textContent = i.toString();
            pageButton.addEventListener('click', () => this.showPage(i));
            paginationUl.appendChild(pageButton);
        }
        
        // Derniere page
        if (endPage < this.totalPages) {
            if (endPage < this.totalPages - 1) {
                const ellipsis = document.createElement('li');
                ellipsis.className = 'pagination-item pagination-ellipsis';
                ellipsis.textContent = '...';
                paginationUl.appendChild(ellipsis);
            }
            
            const lastButton = document.createElement('li');
            lastButton.className = 'pagination-item';
            lastButton.textContent = this.totalPages.toString();
            lastButton.addEventListener('click', () => this.showPage(this.totalPages));
            paginationUl.appendChild(lastButton);
        }
        
        // Bouton suivant
        const nextButton = document.createElement('li');
        nextButton.className = 'pagination-item pagination-next';
        nextButton.innerHTML = 'Suivant &raquo;';
        nextButton.addEventListener('click', () => {
            if (this.currentPage < this.totalPages) {
                this.showPage(this.currentPage + 1);
            }
        });
        paginationUl.appendChild(nextButton);
        
        this.paginationContainer.appendChild(paginationUl);
    }
    
    /**
     * Met a jour l'etat des boutons de pagination
     */
    updatePaginationState() {
        if (!this.paginationContainer) return;
        
        // Mise a jour du bouton precedent
        const prevButton = this.paginationContainer.querySelector('.pagination-prev');
        if (prevButton) {
            prevButton.classList.toggle('pagination-disabled', this.currentPage === 1);
        }
        
        // Mise a jour du bouton suivant
        const nextButton = this.paginationContainer.querySelector('.pagination-next');
        if (nextButton) {
            nextButton.classList.toggle('pagination-disabled', this.currentPage === this.totalPages);
        }
        
        // Mise a jour des boutons de page
        const pageButtons = this.paginationContainer.querySelectorAll('.pagination-item:not(.pagination-prev):not(.pagination-next):not(.pagination-ellipsis)');
        pageButtons.forEach(button => {
            button.classList.toggle('pagination-active', parseInt(button.textContent) === this.currentPage);
        });
    }
    
    /**
     * Permet de mettre a jour les elements lorsqu'ils changent
     * (utile apres filtrage ou tri)
     */
    refresh() {
        this.items = this.container ? this.container.querySelectorAll(this.itemsSelector) : [];
        this.totalPages = Math.ceil(this.items.length / this.itemsPerPage);
        
        // Ajuster la page courante si elle depasse le nouveau total
        if (this.currentPage > this.totalPages) {
            this.currentPage = Math.max(1, this.totalPages);
        }
        
        this.renderPagination();
        this.showPage(this.currentPage);
    }
}
