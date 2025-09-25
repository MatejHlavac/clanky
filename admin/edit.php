<?php
// Admin - editácia článkov
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Kontrola či je admin prihlásený
requireAdmin();

$admin = getCurrentAdmin();
$message = '';
$error = '';

// Získaj ID článku
$clanok_id = $_GET['id'] ?? 0;

if (!$clanok_id) {
    header('Location: dashboard.php');
    exit();
}

// Načítaj článok
$clanok = getClanokById($clanok_id);

if (!$clanok) {
    header('Location: dashboard.php');
    exit();
}

// Spracovanie formulára
if ($_POST) {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    if (empty($title)) {
        $error = 'Názov článku je povinný.';
    } elseif (empty($content)) {
        $error = 'Obsah článku je povinný.';
    } else {
        if (updateClanok($clanok_id, $title, $content)) {
            $message = 'Článok bol úspešne upravený!';
            // Načítaj aktualizovaný článok
            $clanok = getClanokById($clanok_id);
        } else {
            $error = 'Chyba pri úprave článku.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upraviť článok - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- CKEditor - WYSIWYG editor -->
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <style>
        .admin-header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .admin-header h1 {
            margin-bottom: 10px;
        }
        .admin-nav {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .admin-nav a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 4px;
            transition: background 0.3s ease;
        }
        .admin-nav a:hover {
            background: rgba(255,255,255,0.1);
        }
        .admin-nav .active {
            background: #f39c12;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="admin-header">
            <h1>Admin Panel</h1>
            <div class="admin-nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="add.php">Pridať článok</a>
                <a href="edit.php?id=<?php echo $clanok_id; ?>" class="active">Upraviť článok</a>
                <a href="../index.php">Zobraziť stránku</a>
                <a href="logout.php">Odhlásiť sa</a>
            </div>
        </div>

        <main>
            <h2>Upraviť článok</h2>
            
            <?php if ($message): ?>
                <div class="message success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="article-form">
                <div class="form-group">
                    <label for="title">Názov článku:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($clanok['title']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="content">Obsah článku:</label>
                    <textarea id="content" name="content" rows="15" required><?php echo htmlspecialchars($clanok['content']); ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Uložiť zmeny</button>
                    <a href="dashboard.php" class="btn btn-secondary">Zrušiť</a>
                </div>
            </form>
        </main>
    </div>

    <script>
        // CKEditor konfigurácia pre editáciu
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof ClassicEditor !== 'undefined') {
                ClassicEditor
                    .create(document.querySelector('#content'), {
                        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo'],
                        heading: {
                            options: [
                                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                            ]
                        }
                    })
                    .then(editor => {
                        console.log('CKEditor initialized for editing');
                    })
                    .catch(error => {
                        console.error('CKEditor initialization failed:', error);
                    });
            }
        });
    </script>
</body>
</html>
