-- Migration des rôles de simple_array (CSV) vers JSON
-- Symfony 6.4 attend les rôles au format JSON

-- Sauvegarde des données actuelles
-- SELECT id, roles FROM user;

-- Conversion : "ROLE_A,ROLE_B" -> '["ROLE_A","ROLE_B"]'
-- Pour MariaDB 10.4+

-- Etape 1: Convertir les valeurs simple_array en JSON
UPDATE user SET roles = CONCAT('["', REPLACE(roles, ',', '","'), '"]') WHERE roles IS NOT NULL AND roles != '' AND roles NOT LIKE '[%';

-- Etape 2: Mettre un tableau vide pour les rôles vides
UPDATE user SET roles = '[]' WHERE roles IS NULL OR roles = '';

-- Verification
-- SELECT id, roles FROM user LIMIT 10;
