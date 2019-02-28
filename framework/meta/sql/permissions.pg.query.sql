SELECT "permission" FROM "permissions" WHERE "userId" = %d AND "permission" IS NOT NULL
UNION DISTINCT
SELECT "permission" FROM "permissions" WHERE "group" IN (SELECT "group" FROM "permissions" WHERE "userId" = %r AND "group" IS NOT NULL) AND "permission" IS NOT NULL