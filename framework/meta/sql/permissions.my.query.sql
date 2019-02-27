SELECT `permission` FROM `permissions` WHERE `userId` = %d AND NOT ISNULL(`permission`)
UNION DISTINCT
SELECT `permission` FROM `permissions` WHERE `group` IN (SELECT `group` FROM `permissions` WHERE `userId` = %d AND NOT ISNULL(`group`)) AND NOT ISNULL(`permission`)