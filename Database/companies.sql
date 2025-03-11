-- Table des entreprises
CREATE TABLE IF NOT EXISTS companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    postal_code VARCHAR(10) NOT NULL,
    city VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL DEFAULT 'France',
    phone VARCHAR(20),
    email VARCHAR(100),
    website VARCHAR(255),
    description TEXT,
    sector VARCHAR(100),
    size ENUM('TPE', 'PME', 'ETI', 'GE') COMMENT 'Taille: TPE, PME, ETI, GE',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des évaluations d'entreprises
CREATE TABLE IF NOT EXISTS company_ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    user_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    -- Un utilisateur ne peut évaluer une entreprise qu'une seule fois
    UNIQUE KEY unique_rating (company_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des offres de stage (référencée pour les statistiques)
CREATE TABLE IF NOT EXISTS offers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertion de quelques entreprises d'exemple
INSERT INTO companies (name, address, postal_code, city, phone, email, website, description, sector, size) VALUES
('TechInnovate', '15 rue de la Technologie', '75001', 'Paris', '0123456789', 'contact@techinnovate.fr', 'www.techinnovate.fr', 'Entreprise spécialisée dans le développement logiciel', 'Technologie', 'PME'),
('EcoSolutions', '42 avenue Verte', '69002', 'Lyon', '0487654321', 'info@ecosolutions.fr', 'www.ecosolutions.fr', 'Solutions écologiques pour entreprises', 'Environnement', 'ETI'),
('ConseilPlus', '8 boulevard des Consultants', '33000', 'Bordeaux', '0556789012', 'contact@conseilplus.fr', 'www.conseilplus.fr', 'Cabinet de conseil en management', 'Conseil', 'TPE');
