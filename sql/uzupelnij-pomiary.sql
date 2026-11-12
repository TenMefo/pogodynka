-- Uruchom cały skrypt w konsoli SQLite podłączonej do bazy aplikacji.
-- Dodajemy tylko brakujące dane. Ponowne uruchomienie nie tworzy duplikatów.
BEGIN IMMEDIATE;

INSERT INTO location (city, country, latitude, longitude)
SELECT 'Szczecin', 'PL', 53.4289, 14.553
WHERE NOT EXISTS (
    SELECT 1 FROM location WHERE city = 'Szczecin' AND country = 'PL'
);

INSERT INTO location (city, country, latitude, longitude)
SELECT 'Police', 'PL', 53.5521, 14.5718
WHERE NOT EXISTS (
    SELECT 1 FROM location WHERE city = 'Police' AND country = 'PL'
);

-- Pięć przykładowych rekordów na pięć kolejnych dni.
WITH samples (day_offset, city, celsius) AS (
    VALUES
        (1, 'Szczecin', 12),
        (2, 'Police', 11),
        (3, 'Szczecin', 14),
        (4, 'Police', 13),
        (5, 'Szczecin', 10)
),
candidates AS (
    SELECT
        s.day_offset,
        date('now', 'localtime', '+' || s.day_offset || ' days') AS measurement_date,
        s.celsius,
        (SELECT MIN(l.id) FROM location l WHERE l.city = s.city AND l.country = 'PL') AS location_id
    FROM samples s
)
INSERT INTO measurement (location_id, date, celsius)
SELECT c.location_id, c.measurement_date, c.celsius
FROM candidates c
WHERE NOT EXISTS (
    SELECT 1 FROM measurement m
    WHERE m.location_id = c.location_id AND m.date = c.measurement_date
)
ORDER BY c.day_offset
LIMIT MAX(0, 5 - (SELECT COUNT(*) FROM measurement));

COMMIT;

-- Wynik do sprawdzenia i zrzutu ekranu: miasto, data i temperatura.
SELECT m.id, l.city, l.country, m.date, m.celsius
FROM measurement m
JOIN location l ON l.id = m.location_id
ORDER BY m.date, m.id;
