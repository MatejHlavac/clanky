-- SQL skript pre vytvorenie databázy a tabuľky článkov

-- Vytvorenie databázy (ak neexistuje)
CREATE DATABASE IF NOT EXISTS clanky_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Použitie databázy
USE clanky_db;

-- Vytvorenie tabuľky pre články
CREATE TABLE IF NOT EXISTS clanky (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vloženie ukážkových dát (voliteľné)
INSERT INTO clanky (title, content) VALUES 
('Vitajte na našej stránke', 'Toto je prvý článok na našej stránke. Môžete ho upraviť alebo zmazať a pridať vlastné články.'),
('Ako používať túto aplikáciu', 'Táto aplikácia umožňuje jednoduché pridávanie a zobrazovanie článkov. Stačí kliknúť na "Pridať článok" a vyplniť formulár.');
