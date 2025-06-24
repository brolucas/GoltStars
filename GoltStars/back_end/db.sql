-- TABLE client
CREATE TABLE client (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL
) ENGINE=InnoDB;

-- TABLE adresse (liée à un client)
CREATE TABLE adresse (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rue VARCHAR(255) NOT NULL,
    code_postal VARCHAR(10) NOT NULL,
    ville VARCHAR(100) NOT NULL,
    pays VARCHAR(100) NOT NULL,
    client_id INT NOT NULL,
    FOREIGN KEY (client_id) REFERENCES client(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- TABLE carte_bancaire (liée à un client et à une adresse de facturation)
CREATE TABLE carte_bancaire (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(16) NOT NULL,
    date_expiration DATE NOT NULL,
    cryptogramme VARCHAR(3) NOT NULL,
    client_id INT NOT NULL,
    adresse_facturation_id INT NOT NULL,
    FOREIGN KEY (client_id) REFERENCES client(id) ON DELETE CASCADE,
    FOREIGN KEY (adresse_facturation_id) REFERENCES adresse(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- TABLE produit
CREATE TABLE produit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prix DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB;

-- TABLE categorie
CREATE TABLE categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- TABLE produit_categorie
CREATE TABLE produit_categorie (
    produit_id INT NOT NULL,
    categorie_id INT NOT NULL,
    PRIMARY KEY (produit_id, categorie_id),
    FOREIGN KEY (produit_id) REFERENCES produit(id) ON DELETE CASCADE,
    FOREIGN KEY (categorie_id) REFERENCES categorie(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- TABLE commande (lié à client, carte utilisée, adresse de livraison)
CREATE TABLE commande (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_commande DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    client_id INT NOT NULL,
    carte_bancaire_id INT NOT NULL,
    adresse_livraison_id INT NOT NULL,
    FOREIGN KEY (client_id) REFERENCES client(id) ON DELETE CASCADE,
    FOREIGN KEY (carte_bancaire_id) REFERENCES carte_bancaire(id) ON DELETE CASCADE,
    FOREIGN KEY (adresse_livraison_id) REFERENCES adresse(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- TABLE commande_produit (relation N-N entre commandes et produits)
CREATE TABLE commande_produit (
    commande_id INT NOT NULL,
    produit_id INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    PRIMARY KEY (commande_id, produit_id),
    FOREIGN KEY (commande_id) REFERENCES commande(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produit(id) ON DELETE CASCADE
) ENGINE=InnoDB;
