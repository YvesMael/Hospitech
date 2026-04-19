-- Création de la base de données
CREATE DATABASE IF NOT EXISTS HOSPITECH_BD;
USE HOSPITECH_BD;

-- 1. Table Service
CREATE TABLE Service (
    id_service INT AUTO_INCREMENT PRIMARY KEY,
    nom_service VARCHAR(100)
) ENGINE=InnoDB;

-- 2. Table Categorie
CREATE TABLE Categorie (
    num_categorie INT AUTO_INCREMENT PRIMARY KEY,
    nom_categorie VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- 3. Table Salle
CREATE TABLE Salle (
    num_salle INT AUTO_INCREMENT PRIMARY KEY,
    nom_salle VARCHAR(100) NOT NULL,
    id_service INT,
    CONSTRAINT fk_salle_service FOREIGN KEY (id_service) REFERENCES Service(id_service)
) ENGINE=InnoDB;

-- 4. Table Equipement 
-- Note : Le TYPE OBJECT FREQUENCE est remplacé par des colonnes INT
CREATE TABLE Equipement (
    code_equip VARCHAR(50) NOT NULL PRIMARY KEY,
    num_equip INT UNIQUE NOT NULL,
    marque VARCHAR(100),
    modele VARCHAR(100),
    etat_equip ENUM('en panne', 'en maintenance', 'en fonctionnement'),
    date_ajout DATE,
    -- Éclatement du type FREQUENCE
    freq_jours INT DEFAULT 0,
    freq_mois INT DEFAULT 0,
    freq_ans INT DEFAULT 0,
    num_salle INT,
    date_prochaine_maintenance DATE,
    id_categorie INT,
    CONSTRAINT fk_equip_categorie FOREIGN KEY (id_categorie) REFERENCES Categorie(num_categorie),
    CONSTRAINT fk_equip_salle FOREIGN KEY (num_salle) REFERENCES Salle(num_salle)
) ENGINE=InnoDB;

-- 5. Table Utilisateur
CREATE TABLE Utilisateur (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100),
    password VARCHAR(250) NOT NULL,
    adresse_mail VARCHAR(100),
    id_service INT,
    CONSTRAINT fk_user_service FOREIGN KEY (id_service) REFERENCES Service(id_service)
) ENGINE=InnoDB;

-- 6. Table Technicien
CREATE TABLE Technicien (
    id_utilisateur INT PRIMARY KEY,
    titre VARCHAR(100),
    CONSTRAINT fk_tech_user FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id_utilisateur) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 7. Table de liaison Categorie_technicien
CREATE TABLE Categorie_technicien (
    id_cat_tech INT AUTO_INCREMENT PRIMARY KEY,
    id_categorie INT,
    id_technicien INT,
    CONSTRAINT fk_tech FOREIGN KEY (id_technicien) REFERENCES Technicien(id_utilisateur) ON DELETE CASCADE,
    CONSTRAINT fk_cat FOREIGN KEY (id_categorie) REFERENCES Categorie(num_categorie) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 8. Table Personnel_medical
CREATE TABLE Personnel_medical (
    id_utilisateur INT PRIMARY KEY,
    CONSTRAINT fk_perso_user FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id_utilisateur) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 9. Table Maintenance
CREATE TABLE Maintenance (
    num_maintenance INT AUTO_INCREMENT PRIMARY KEY,
    date_heure DATETIME,
    diagnostic TEXT,
    actions_effectuees TEXT,
    date_remise_service DATE,
    num_equip_ref INT NOT NULL, -- On utilise num_equip (UNIQUE) ou code_equip
    id_technicien INT NOT NULL,
    -- Liaison via num_equip (il doit être défini comme UNIQUE dans Equipement)
    CONSTRAINT fk_maint_equip FOREIGN KEY (num_equip_ref) REFERENCES Equipement(num_equip),
    CONSTRAINT fk_maint_tech FOREIGN KEY (id_technicien) REFERENCES Technicien(id_utilisateur)
) ENGINE=InnoDB;

-- 10. Table Maint_Preventive
CREATE TABLE Maint_Preventive (
    num_maintenance INT PRIMARY KEY,
    CONSTRAINT fk_prev_maint FOREIGN KEY (num_maintenance) REFERENCES Maintenance(num_maintenance) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 11. Table Maint_Corrective
CREATE TABLE Maint_Corrective (
    num_maintenance INT PRIMARY KEY,
    date_apparit_panne DATE,
    description_panne TEXT,
    id_personnel_medical INT,
    statut_maint ENUM('à jour', 'proche', 'non réalisée'),
    CONSTRAINT fk_corr_maint FOREIGN KEY (num_maintenance) REFERENCES Maintenance(num_maintenance) ON DELETE CASCADE,
    CONSTRAINT fk_corr_perso FOREIGN KEY (id_personnel_medical) REFERENCES Personnel_medical(id_utilisateur)
) ENGINE=InnoDB;
DELIMITER //

CREATE TRIGGER before_insert_equipement
BEFORE INSERT ON Equipement
FOR EACH ROW
BEGIN
    -- Calcul : Date d'ajout + Ans + Mois + Jours
    SET NEW.date_prochaine_maintenance = DATE_ADD(
        DATE_ADD(
            DATE_ADD(NEW.date_ajout, INTERVAL NEW.freq_ans YEAR),
            INTERVAL NEW.freq_mois MONTH
        ),
        INTERVAL NEW.freq_jours DAY
    );
END //

DELIMITER ;