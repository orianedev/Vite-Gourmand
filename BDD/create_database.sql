CREATE DATABASE vite_gourmand;
USE vite_gourmand;

-- TABLE ROLE
CREATE TABLE role (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);

-- TABLE UTILISATEUR
CREATE TABLE utilisateur (
    utilisateur_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    prenom VARCHAR(50),
    telephone VARCHAR(20),
    ville VARCHAR(50),
    pays VARCHAR(50),
    adresse_postale VARCHAR(255),
    role_id INT,
    FOREIGN KEY (role_id) REFERENCES role(role_id)
);

-- TABLE THEME
CREATE TABLE theme (
    theme_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);

-- TABLE REGIME
CREATE TABLE regime (
    regime_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);

-- TABLE MENU
CREATE TABLE menu (
    menu_id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    nombre_personne_minimum INT,
    prix_par_personne DECIMAL(10,2),
    description TEXT,
    quantite_restante INT,
    theme_id INT,
    regime_id INT,
    FOREIGN KEY (theme_id) REFERENCES theme(theme_id),
    FOREIGN KEY (regime_id) REFERENCES regime(regime_id)
);

-- TABLE PLAT
CREATE TABLE plat (
    plat_id INT AUTO_INCREMENT PRIMARY KEY,
    titre_plat VARCHAR(100) NOT NULL,
    photo VARCHAR(255)
);

-- TABLE MENU_PLAT (relation plusieurs à plusieurs)
CREATE TABLE menu_plat (
    menu_id INT,
    plat_id INT,
    PRIMARY KEY (menu_id, plat_id),
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id),
    FOREIGN KEY (plat_id) REFERENCES plat(plat_id)
);

-- TABLE ALLERGENE
CREATE TABLE allergene (
    allergene_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50)
);

-- TABLE PLAT_ALLERGENE
CREATE TABLE plat_allergene (
    plat_id INT,
    allergene_id INT,
    PRIMARY KEY (plat_id, allergene_id),
    FOREIGN KEY (plat_id) REFERENCES plat(plat_id),
    FOREIGN KEY (allergene_id) REFERENCES allergene(allergene_id)
);

-- TABLE COMMANDE
CREATE TABLE commande (
    numero_commande INT AUTO_INCREMENT PRIMARY KEY,
    date_commande DATE,
    date_prestation DATE,
    heure_livraison TIME,
    prix_menu DECIMAL(10,2),
    nombre_personne INT,
    prix_livraison DECIMAL(10,2),
    statut VARCHAR(50),
    pret_materiel BOOLEAN,
    restitution_materiel BOOLEAN,
    utilisateur_id INT,
    menu_id INT,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id),
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id)
);

-- TABLE AVIS
CREATE TABLE avis (
    avis_id INT AUTO_INCREMENT PRIMARY KEY,
    note INT,
    description TEXT,
    statut VARCHAR(50),
    utilisateur_id INT,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id)
);

-- TABLE HORAIRE
CREATE TABLE horaire (
    horaire_id INT AUTO_INCREMENT PRIMARY KEY,
    jour VARCHAR(20),
    heure_ouverture TIME,
    heure_fermeture TIME
);