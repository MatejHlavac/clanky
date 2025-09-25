<?php
// Autentifikačné funkcie pre admin systém

/**
 * Spustí session ak nie je spustená
 */
function startSession() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Prihlási admina
 */
function loginAdmin($username, $password) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT id, username, password, full_name FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            startSession();
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_name'] = $admin['full_name'];
            
            // Aktualizuj posledné prihlásenie
            updateLastLogin($admin['id']);
            
            return true;
        }
        
        return false;
    } catch(PDOException $e) {
        error_log("Chyba pri prihlasovaní admina: " . $e->getMessage());
        return false;
    }
}

/**
 * Odhlási admina
 */
function logoutAdmin() {
    startSession();
    session_destroy();
    return true;
}

/**
 * Kontroluje či je používateľ prihlásený ako admin
 */
function isAdminLoggedIn() {
    startSession();
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Vracia informácie o prihlásenom adminovi
 */
function getCurrentAdmin() {
    if (!isAdminLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['admin_id'],
        'username' => $_SESSION['admin_username'],
        'name' => $_SESSION['admin_name']
    ];
}

/**
 * Aktualizuje posledné prihlásenie admina
 */
function updateLastLogin($adminId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$adminId]);
    } catch(PDOException $e) {
        error_log("Chyba pri aktualizácii posledného prihlásenia: " . $e->getMessage());
    }
}

/**
 * Presmeruje na prihlasovaciu stránku ak nie je admin prihlásený
 */
function requireAdmin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Presmeruje na admin panel ak je admin prihlásený
 */
function redirectIfLoggedIn() {
    if (isAdminLoggedIn()) {
        header('Location: dashboard.php');
        exit();
    }
}

/**
 * Vytvorí nového admina (len pre setup)
 */
function createAdmin($username, $password, $email = '', $fullName = '') {
    global $pdo;
    
    try {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admins (username, password, email, full_name) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$username, $hashedPassword, $email, $fullName]);
    } catch(PDOException $e) {
        error_log("Chyba pri vytváraní admina: " . $e->getMessage());
        return false;
    }
}
?>
