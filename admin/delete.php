<?php
// Admin - mazanie článkov
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Kontrola či je admin prihlásený
requireAdmin();

// Získaj ID článku
$clanok_id = $_GET['id'] ?? 0;

if (!$clanok_id) {
    header('Location: dashboard.php');
    exit();
}

// Načítaj článok pre potvrdenie
$clanok = getClanokById($clanok_id);

if (!$clanok) {
    header('Location: dashboard.php');
    exit();
}

// Spracovanie mazania
if ($_POST && isset($_POST['confirm_delete'])) {
    if (deleteClanok($clanok_id)) {
        header('Location: dashboard.php?deleted=1');
        exit();
    } else {
        $error = 'Chyba pri mazaní článku.';
    }
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zmazať článok - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
            background: #e74c3c;
        }
        .delete-warning {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .delete-warning h3 {
            margin-top: 0;
            color: #721c24;
        }
        .article-preview {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .article-preview h4 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .article-preview .meta {
            color: #7f8c8d;
            font-size: 0.9em;
            margin-bottom: 15px;
        }
        .article-preview .content {
            color: #555;
            line-height: 1.6;
        }
        .delete-actions {
            display: flex;
            gap: 15px;
        }
        .btn-danger {
            background: #e74c3c;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .btn-danger:hover {
            background: #c0392b;
        }
        .btn-secondary {
            background: #95a5a6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            transition: background 0.3s ease;
        }
        .btn-secondary:hover {
            background: #7f8c8d;
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
                <a href="edit.php?id=<?php echo $clanok_id; ?>">Upraviť článok</a>
                <a href="delete.php?id=<?php echo $clanok_id; ?>" class="active">Zmazať článok</a>
                <a href="../index.php">Zobraziť stránku</a>
                <a href="logout.php">Odhlásiť sa</a>
            </div>
        </div>

        <main>
            <h2>Zmazať článok</h2>
            
            <?php if (isset($error)): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="delete-warning">
                <h3>⚠️ Pozor!</h3>
                <p>Chystáte sa zmazať tento článok. Táto akcia je <strong>nevratná</strong>!</p>
            </div>

            <div class="article-preview">
                <h4><?php echo htmlspecialchars($clanok['title']); ?></h4>
                <div class="article-meta">
                    Vytvorený: <?php echo date('d.m.Y H:i', strtotime($clanok['date_created'])); ?>
                    <?php if ($clanok['date_updated'] != $clanok['date_created']): ?>
                        | Upravený: <?php echo date('d.m.Y H:i', strtotime($clanok['date_updated'])); ?>
                    <?php endif; ?>
                </div>
                <div class="article-content">
                    <?php echo nl2br(htmlspecialchars(substr($clanok['content'], 0, 300))); ?>
                    <?php if (strlen($clanok['content']) > 300): ?>...<?php endif; ?>
                </div>
            </div>

            <form method="POST">
                <div class="delete-actions">
                    <button type="submit" name="confirm_delete" class="btn-danger" 
                            onclick="return confirm('Naozaj chcete zmazať tento článok? Táto akcia je nevratná!')">
                        Áno, zmazať článok
                    </button>
                    <a href="dashboard.php" class="btn-secondary">Zrušiť</a>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
