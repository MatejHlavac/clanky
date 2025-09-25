<?php
// Admin - pridávanie nových článkov
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Kontrola či je admin prihlásený
requireAdmin();

$admin = getCurrentAdmin();
$message = '';
$error = '';

if ($_POST) {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    if (empty($title)) {
        $error = 'Názov článku je povinný.';
    } elseif (empty($content)) {
        $error = 'Obsah článku je povinný.';
    } else {
        if (addClanok($title, $content)) {
            $message = 'Článok bol úspešne pridaný!';
            // Vyčistiť formulár
            $title = '';
            $content = '';
        } else {
            $error = 'Chyba pri pridávaní článku.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pridať článok - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- CKEditor - jednoduchší editor -->
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
            background: #3498db;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="admin-header">
            <h1>Admin Panel</h1>
            <div class="admin-nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="add.php" class="active">Pridať článok</a>
                <a href="../index.php">Zobraziť stránku</a>
                <a href="logout.php">Odhlásiť sa</a>
            </div>
        </div>

        <main>
            <h2>Pridať nový článok</h2>
            
            <?php if ($message): ?>
                <div class="message success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="article-form">
                <div class="form-group">
                    <label for="title">Názov článku:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="content">Obsah článku:</label>
                    <textarea id="content" name="content" rows="15" required><?php echo htmlspecialchars($content ?? ''); ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Pridať článok</button>
                    <a href="dashboard.php" class="btn btn-secondary">Zrušiť</a>
                </div>
            </form>
        </main>
    </div>

    <script>
        // CKEditor konfigurácia
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
                        console.log('CKEditor initialized successfully');
                    })
                    .catch(error => {
                        console.error('CKEditor initialization failed:', error);
                    });
            }
        });
    </script>
</body>
</html>
