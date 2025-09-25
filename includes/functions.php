<?php
// Pomocné funkcie pre správu článkov

/**
 * Získa všetky články z databázy
 */
function getClanky() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM clanky ORDER BY date_created DESC");
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Chyba pri načítavaní článkov: " . $e->getMessage());
        return [];
    }
}

/**
 * Pridá nový článok do databázy
 */
function addClanok($title, $content) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO clanky (title, content, date_created) VALUES (?, ?, NOW())");
        return $stmt->execute([$title, $content]);
    } catch(PDOException $e) {
        error_log("Chyba pri pridávaní článku: " . $e->getMessage());
        return false;
    }
}

/**
 * Získa konkrétny článok podľa ID
 */
function getClanokById($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM clanky WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch(PDOException $e) {
        error_log("Chyba pri načítavaní článku: " . $e->getMessage());
        return false;
    }
}
?>
