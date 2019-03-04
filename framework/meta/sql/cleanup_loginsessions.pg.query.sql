DELETE
FROM "loginsessions"
WHERE "lastUpdate" > extract(epoch from NOW() - INTERVAL '1 hour')