-- Les Techniciens aussi appartiendront à un service qui sera créé pour eux
-- ceci pour nous permettre de relier uniquement service à Hopital
-- et donc un utilisateur travaillant dans un service et un service appartenant
-- à un hopital, on peut créer plusieurs hopitaux et on saura dans lequel chacun travaille

-- Note : Sur Render/PostgreSQL, la base de données est créée via l'interface. 
-- Pas de "USE" ou de "CREATE DATABASE" dans le script.

-- Création des types énumérés (ENUM)
CREATE TYPE etat_equip_enum AS ENUM ('en panne', 'en maintenance', 'en fonctionnement');
CREATE TYPE statut_maint_enum AS ENUM ('à jour', 'proche', 'non réalisée');

-- 0. Table Hopital
CREATE TABLE Hopital (
    id_hopital SERIAL PRIMARY KEY,
    nom_hopital VARCHAR(100),
    adresse VARCHAR(100),
    telephone VARCHAR(50)
);

-- 1. Table Service
CREATE TABLE Service (
    id_service SERIAL PRIMARY KEY,
    nom_service VARCHAR(100),
    id_hopital INT,
    CONSTRAINT fk_hop FOREIGN KEY (id_hopital) REFERENCES Hopital(id_hopital)
);

-- 2. Table Categorie
CREATE TABLE Categorie (
    num_categorie SERIAL PRIMARY KEY,
    nom_categorie VARCHAR(100) NOT NULL
);

-- 3. Table Salle
CREATE TABLE Salle (
    num_salle SERIAL PRIMARY KEY,
    nom_salle VARCHAR(100) NOT NULL,
    id_service INT,
    CONSTRAINT fk_salle_service FOREIGN KEY (id_service) REFERENCES Service(id_service)
);

-- 4. Table Equipement
CREATE TABLE Equipement (
    code_equip VARCHAR(50) NOT NULL PRIMARY KEY,
    num_equip INT UNIQUE NOT NULL,
    marque VARCHAR(100),
    modele VARCHAR(100),
    etat_equip etat_equip_enum,
    date_ajout DATE,
    freq_jours INT DEFAULT 0,
    freq_mois INT DEFAULT 0,
    freq_ans INT DEFAULT 0,
    num_salle INT,
    date_prochaine_maintenance DATE,
    id_categorie INT,
    id_hopital INT,
    CONSTRAINT fk_equip_hopital FOREIGN KEY (id_hopital) REFERENCES Hopital(id_hopital),
    CONSTRAINT fk_equip_categorie FOREIGN KEY (id_categorie) REFERENCES Categorie(num_categorie),
    CONSTRAINT fk_equip_salle FOREIGN KEY (num_salle) REFERENCES Salle(num_salle)
);

-- 5. Table Utilisateur
CREATE TABLE Utilisateur (
    id_utilisateur SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100),
    password VARCHAR(250) NOT NULL,
    adresse_mail VARCHAR(100) UNIQUE,
    id_service INT,
    CONSTRAINT fk_user_service FOREIGN KEY (id_service) REFERENCES Service(id_service)
);

-- 6. Table Technicien
CREATE TABLE Technicien (
    id_utilisateur INT PRIMARY KEY,
    titre VARCHAR(100),
    CONSTRAINT fk_tech_user FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id_utilisateur) ON DELETE CASCADE
);

-- 7. Table de liaison Categorie_technicien
CREATE TABLE Categorie_technicien (
    id_cat_tech SERIAL PRIMARY KEY,
    id_categorie INT,
    id_technicien INT,
    CONSTRAINT fk_tech FOREIGN KEY (id_technicien) REFERENCES Technicien(id_utilisateur) ON DELETE CASCADE,
    CONSTRAINT fk_cat FOREIGN KEY (id_categorie) REFERENCES Categorie(num_categorie) ON DELETE CASCADE
);

-- 8. Table Personnel_medical
CREATE TABLE Personnel_medical (
    id_utilisateur INT PRIMARY KEY,
    CONSTRAINT fk_perso_user FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id_utilisateur) ON DELETE CASCADE
);

-- 9. Table Maintenance
CREATE TABLE Maintenance (
    num_maintenance SERIAL PRIMARY KEY,
    date_heure TIMESTAMP,
    diagnostic TEXT,
    actions_effectuees TEXT,
    date_remise_service DATE,
    num_equip_ref INT NOT NULL,
    id_technicien INT NOT NULL,
    CONSTRAINT fk_maint_equip FOREIGN KEY (num_equip_ref) REFERENCES Equipement(num_equip),
    CONSTRAINT fk_maint_tech FOREIGN KEY (id_technicien) REFERENCES Technicien(id_utilisateur)
);

-- 10. Table Maint_Preventive
CREATE TABLE Maint_Preventive (
    num_maintenance INT PRIMARY KEY,
    CONSTRAINT fk_prev_maint FOREIGN KEY (num_maintenance) REFERENCES Maintenance(num_maintenance) ON DELETE CASCADE
);

-- 11. Table Maint_Corrective
CREATE TABLE Maint_Corrective (
    num_maintenance INT PRIMARY KEY,
    date_apparit_panne DATE,
    description_panne TEXT,
    id_personnel_medical INT,
    statut_maint statut_maint_enum,
    CONSTRAINT fk_corr_maint FOREIGN KEY (num_maintenance) REFERENCES Maintenance(num_maintenance) ON DELETE CASCADE,
    CONSTRAINT fk_corr_perso FOREIGN KEY (id_personnel_medical) REFERENCES Personnel_medical(id_utilisateur)
);

-- LOGIQUE DU TRIGGER (Version PostgreSQL)

-- 1. On crée d'abord la fonction du trigger
CREATE OR REPLACE FUNCTION fn_calcul_prochaine_maintenance()
RETURNS TRIGGER AS $$
BEGIN
    -- Dans PostgreSQL, on calcule les intervalles ainsi :
    NEW.date_prochaine_maintenance := NEW.date_ajout 
        + (NEW.freq_ans * INTERVAL '1 year')
        + (NEW.freq_mois * INTERVAL '1 month')
        + (NEW.freq_jours * INTERVAL '1 day');
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- 2. On lie la fonction à la table Equipement
CREATE TRIGGER trg_before_insert_equipement
BEFORE INSERT ON Equipement
FOR EACH ROW
EXECUTE FUNCTION fn_calcul_prochaine_maintenance();