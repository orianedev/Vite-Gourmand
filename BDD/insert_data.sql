-- =========================
-- ROLES
-- =========================
INSERT INTO role (libelle) VALUES
('client'),
('employe'),
('admin');

-- =========================
-- UTILISATEURS
-- =========================
INSERT INTO utilisateur (email, password, prenom, telephone, ville, pays, adresse_postale, role_id) VALUES
('oriane@gmail.com', '1234', 'Oriane', '0600000001', 'Paris', 'France', '12 rue Victor Hugo', 1),
('lucie@gmail.com', '1234', 'Lucie', '0600000002', 'Lyon', 'France', '5 avenue Jean Jaures', 1),
('mehdi@gmail.com', '1234', 'Mehdi', '0600000003', 'Marseille', 'France', '8 rue du Port', 1),
('sarah@gmail.com', '1234', 'Sarah', '0600000004', 'Toulouse', 'France', '10 rue Rose', 1),
('nina@gmail.com', '1234', 'Nina', '0600000005', 'Nice', 'France', '15 rue Soleil', 1),

('paul@vg.com', '1234', 'Paul', '0600000006', 'Paris', 'France', '3 rue Travail', 2),
('jade@vg.com', '1234', 'Jade', '0600000007', 'Paris', 'France', '4 rue Travail', 2),

('admin@vg.com', 'admin123', 'Admin', '0600000008', 'Paris', 'France', '1 rue Siege', 3);

-- =========================
-- THEMES
-- =========================
INSERT INTO theme (libelle) VALUES
('Italien'),
('Asiatique'),
('Oriental'),
('Vegan'),
('Buffet');

-- =========================
-- REGIMES
-- =========================
INSERT INTO regime (libelle) VALUES
('Classique'),
('Vegetarien'),
('Halal'),
('Sans Gluten'),
('Vegan');

-- =========================
-- MENUS
-- =========================
INSERT INTO menu (titre, nombre_personne_minimum, prix_par_personne, description, quantite_restante, theme_id, regime_id) VALUES
('Menu Italien', 5, 18.90, 'Entree + plat + dessert', 20, 1, 1),
('Menu Sushi', 4, 22.50, 'Plateau japonais complet', 15, 2, 1),
('Menu Couscous', 6, 19.90, 'Menu oriental complet', 10, 3, 3),
('Menu Vegan Green', 3, 17.50, 'Cuisine vegetale complete', 12, 4, 5),
('Menu Buffet Prestige', 10, 25.00, 'Grand buffet varie', 8, 5, 1);

-- =========================
-- PLATS
-- =========================
INSERT INTO plat (titre_plat, photo, categorie) VALUES
('Bruschetta', 'bruschetta.jpg', 'entree'),
('Salade Cesar', 'salade.jpg', 'entree'),
('Lasagnes', 'lasagnes.jpg', 'plat'),
('Pizza Margherita', 'pizza.jpg', 'plat'),
('Tiramisu', 'tiramisu.jpg', 'dessert'),
('Panna Cotta', 'panna.jpg', 'dessert'),
('Eau Plate', 'eau.jpg', 'boisson'),
('Coca Cola', 'coca.jpg', 'boisson'),

('Soupe Miso', 'miso.jpg', 'entree'),
('Sushi Saumon', 'sushi.jpg', 'plat'),
('Mochi', 'mochi.jpg', 'dessert'),
('The Glace', 'the.jpg', 'boisson'),

('Houmous', 'houmous.jpg', 'entree'),
('Couscous Royal', 'couscous.jpg', 'plat'),
('Baklava', 'baklava.jpg', 'dessert'),
('Jus Orange', 'jus.jpg', 'boisson'),

('Salade Quinoa', 'quinoa.jpg', 'entree'),
('Burger Vegan', 'burger.jpg', 'plat'),
('Brownie Vegan', 'brownie.jpg', 'dessert'),
('Smoothie Vert', 'smoothie.jpg', 'boisson');

-- =========================
-- MENU_PLAT
-- =========================
INSERT INTO menu_plat (menu_id, plat_id) VALUES
(1,1),(1,3),(1,5),(1,8),
(2,9),(2,10),(2,11),(2,12),
(3,13),(3,14),(3,15),(3,16),
(4,17),(4,18),(4,19),(4,20),
(5,2),(5,4),(5,6),(5,7);

-- =========================
-- ALLERGENES
-- =========================
INSERT INTO allergene (libelle) VALUES
('Gluten'),
('Lait'),
('Poisson'),
('Oeuf'),
('Fruits a coque');

-- =========================
-- PLAT_ALLERGENE
-- =========================
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES
(1,1),
(3,1),
(5,2),
(10,3),
(15,5),
(19,4);

-- =========================
-- COMMANDES
-- =========================
INSERT INTO commande (date_commande, date_prestation, heure_livraison, prix_menu, nombre_personne, prix_livraison, statut, pret_materiel, restitution_materiel, utilisateur_id, menu_id) VALUES
('2026-04-01','2026-04-10','12:00:00',94.50,5,10.00,'confirmee',1,0,1,1),
('2026-04-02','2026-04-12','19:00:00',90.00,4,8.00,'livree',0,0,2,2),
('2026-04-03','2026-04-15','13:00:00',119.40,6,12.00,'en preparation',1,0,3,3),
('2026-04-04','2026-04-18','20:00:00',52.50,3,7.00,'livree',0,0,4,4),
('2026-04-05','2026-04-20','18:30:00',250.00,10,15.00,'confirmee',1,0,5,5);

-- =========================
-- AVIS
-- =========================
INSERT INTO avis (note, description, statut, utilisateur_id) VALUES
(5,'Excellent service','publie',1),
(4,'Tres bon repas','publie',2),
(5,'Je recommande vivement','publie',3),
(3,'Livraison un peu longue','publie',4),
(5,'Parfait pour anniversaire','publie',5);

-- =========================
-- HORAIRES
-- =========================
INSERT INTO horaire (jour, heure_ouverture, heure_fermeture) VALUES
('Lundi','09:00:00','18:00:00'),
('Mardi','09:00:00','18:00:00'),
('Mercredi','09:00:00','18:00:00'),
('Jeudi','09:00:00','18:00:00'),
('Vendredi','09:00:00','22:00:00'),
('Samedi','10:00:00','22:00:00'),
('Dimanche','10:00:00','16:00:00');