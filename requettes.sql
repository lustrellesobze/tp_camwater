-- A. insertion de 05 abonnée 
insert into abonnes (nom,prenom,ville,quartier,numerocompteur,typeabonnement)
VALUES('Frederic','Christian','Douala','elf','cp50557','Professionnel'),
       ('André','Christian','Douala','elf','cp505555','Domestique'),
       ('Cedric','Christian','Douala','elf','cp50557004','Professionnel'),
       ('Edouard','Christian','Garoua','Logbessou','cp50155574','Domestique'),
       ('Frederic','Roberto','Garoua','elf','cp50145574','Professionnel');


--  A insertion de 3 facture
insert into factures (abonne_id,consommation,montanttoal,dateEmission,statut)
 VALUES(4,250,250000,'2025-05-12','non payé'),
        (5,250,250000,'2025-05-12','payé'),
        (6,250,250000,'2025-05-12','non payé');
 



-- B Jointure pour aficher pour chque facture le  no complete de l'abone ville consommation et montant total
SELECT ab.nom,ab.prenom,ab.ville,fa.consommation,fa.montanttoal
FROM factures fa 
JOIN abonnes ab 
on fa.abonne_id = ab.abonne_id


--C select avec aggragation  : claculer le montant total des facture par ville 
SELECT SUM(montanttoal),ab.ville
FROM factures fa 
JOIN abonnes ab 
on fa.abonne_id=ab.abonne_id
GROUP by ab.ville

-- D Changer le statut d'une facture de non payé à payé
UPDATE factures
set statut='payé'
WHERE abonne_id=3;

-- Supprimer les reclmmation donc le statu est Resolue
DELETE FROM reclammations
WHERE statut='Réclamé';



-- F Creation de la vue 
CREATE VIEW vue_factures_impayees 
as 
SELECT fa.*,ab.nom,ab.prenom,ab.ville
FROM factures fa 
JOIN abonnes ab 
on fa.abonne_id=ab.abonne_id 
ORDER BY fa.dateEmission DESC;

