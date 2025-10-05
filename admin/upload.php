<?php
// Upload handler pre obrázky
require_once '../config/database.php';
require_once '../includes/auth.php';

// Kontrola či je admin prihlásený
requireAdmin();

// Nastavenia uploadu - použijeme root uploads priečinok
$upload_dir = '../uploads/';
$max_file_size = 5 * 1024 * 1024; // 5MB
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Vytvor upload priečinok ak neexistuje
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Funkcia na validáciu obrázka
function validateImage($file) {
    global $max_file_size, $allowed_types, $allowed_extensions;
    
    // Kontrola veľkosti
    if ($file['size'] > $max_file_size) {
        return ['success' => false, 'message' => 'Súbor je príliš veľký. Maximálna veľkosť je 5MB.'];
    }
    
    // Kontrola typu MIME
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types)) {
        return ['success' => false, 'message' => 'Neplatný typ súboru. Povolené sú len obrázky (JPG, PNG, GIF, WebP).'];
    }
    
    // Kontrola rozšírenia
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowed_extensions)) {
        return ['success' => false, 'message' => 'Neplatné rozšírenie súboru.'];
    }
    
    // Kontrola či je to skutočne obrázok
    $image_info = getimagesize($file['tmp_name']);
    if ($image_info === false) {
        return ['success' => false, 'message' => 'Súbor nie je platný obrázok.'];
    }
    
    return ['success' => true];
}

// Spracovanie uploadu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    // Kontrola či bol súbor nahraný
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'Súbor je príliš veľký.',
            UPLOAD_ERR_FORM_SIZE => 'Súbor je príliš veľký.',
            UPLOAD_ERR_PARTIAL => 'Súbor bol nahraný len čiastočne.',
            UPLOAD_ERR_NO_FILE => 'Žiadny súbor nebol nahraný.',
            UPLOAD_ERR_NO_TMP_DIR => 'Chýba dočasný priečinok.',
            UPLOAD_ERR_CANT_WRITE => 'Nepodarilo sa zapísať súbor.',
            UPLOAD_ERR_EXTENSION => 'Upload bol zastavený rozšírením.'
        ];
        
        $error_message = $error_messages[$file['error']] ?? 'Neznáma chyba pri uploadu.';
        echo json_encode(['success' => false, 'message' => $error_message]);
        exit;
    }
    
    // Validácia obrázka
    $validation = validateImage($file);
    if (!$validation['success']) {
        echo json_encode($validation);
        exit;
    }
    
    // Generovanie unikátneho názvu súboru
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    // Presunutie súboru
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Vráť URL obrázka - relatívna cesta
        $image_url = 'uploads/' . $filename;
        echo json_encode([
            'success' => true,
            'url' => $image_url,
            'filename' => $filename
        ]);
    } else {
        // Debug informácie
        $error_details = [
            'tmp_name' => $file['tmp_name'],
            'filepath' => $filepath,
            'upload_dir_exists' => is_dir($upload_dir),
            'upload_dir_writable' => is_writable($upload_dir),
            'file_exists' => file_exists($file['tmp_name']),
            'upload_error' => $file['error']
        ];
        
        echo json_encode([
            'success' => false, 
            'message' => 'Nepodarilo sa uložiť súbor.',
            'debug' => $error_details
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Neplatná požiadavka.']);
}
?>
