UPDATE user SET roles = CONCAT('["', REPLACE(roles, ',', '","'), '"]') WHERE roles IS NOT NULL AND roles != '' AND roles NOT LIKE '[%';
UPDATE user SET roles = '[]' WHERE roles IS NULL OR roles = '';
