# Správa článkov

Jednoduchá webová aplikácia na správu článkov vytvorená v PHP a MySQL.

## Funkcie

- ✅ Pridávanie nových článkov
- ✅ Zobrazenie zoznamu všetkých článkov
- ✅ Responzívny dizajn
- ✅ Čistý a jednoduchý kód

## Požiadavky

- PHP 7.4 alebo vyšší
- MySQL 5.7 alebo vyšší
- XAMPP (odporúčané pre lokálny vývoj)

## Inštalácia

1. **Naklonujte repozitár:**
   ```bash
   git clone [URL repozitára]
   cd clanky
   ```

2. **Nastavte databázu:**
   - Spustite XAMPP a zapnite Apache a MySQL
   - Otvorte phpMyAdmin (http://localhost/phpmyadmin)
   - Importujte súbor `database/schema.sql` alebo spustite SQL príkazy manuálne

3. **Nastavte pripojenie k databáze:**
   - Upravte súbor `config/database.php` podľa vašich nastavení databázy

4. **Spustite aplikáciu:**
   - Otvorte prehliadač a prejdite na `http://localhost/clanky`

## Štruktúra projektu

```
clanky/
├── assets/
│   └── css/
│       └── style.css          # Štýly aplikácie
├── config/
│   └── database.php           # Konfigurácia databázy
├── database/
│   └── schema.sql             # SQL skript pre databázu
├── includes/
│   └── functions.php          # Pomocné funkcie
├── index.php                  # Hlavná stránka (zoznam článkov)
├── add.php                    # Stránka na pridávanie článkov
├── .gitignore                 # Git ignore súbor
└── README.md                  # Tento súbor
```

## Použitie

1. **Pridanie článku:**
   - Kliknite na "Pridať článok" v navigácii
   - Vyplňte názov a obsah článku
   - Kliknite na "Pridať článok"

2. **Zobrazenie článkov:**
   - Všetky články sa zobrazujú na hlavnej stránke
   - Články sú zoradené od najnovších po najstaršie

## Plánované funkcie

- [ ] Editácia existujúcich článkov
- [ ] Mazanie článkov
- [ ] Upload obrázkov
- [ ] Kategórie článkov
- [ ] Vyhľadávanie
- [ ] Admin autentifikácia

## Technológie

- **Backend:** PHP 7.4+
- **Databáza:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript
- **Styling:** Custom CSS (bez frameworku)

## Licencia

Tento projekt je open source a dostupný pod MIT licenciou.
