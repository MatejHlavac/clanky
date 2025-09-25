<?php
// Admin dashboard - hlavná stránka admin panelu
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Kontrola či je admin prihlásený
requireAdmin();

$admin = getCurrentAdmin();
$clanky = getClanky();

// Správa o úspešnom mazaní
$message = '';
if (isset($_GET['deleted']) && $_GET['deleted'] == '1') {
    $message = 'Článok bol úspešne zmazaný.';
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Správa článkov</title>
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
        .admin-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .admin-welcome {
            font-size: 1.1em;
        }
        .admin-actions {
            display: flex;
            gap: 10px;
        }
        .btn-logout {
            background: #e74c3c;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s ease;
        }
        .btn-logout:hover {
            background: #c0392b;
        }
        .btn-add {
            background: #27ae60;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s ease;
        }
        .btn-add:hover {
            background: #229954;
        }
        .articles-admin {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .articles-header {
            background: #ecf0f1;
            padding: 20px;
            border-bottom: 1px solid #ddd;
        }
        .articles-header h2 {
            margin: 0;
            color: #2c3e50;
        }
        .article-admin-item {
            padding: 20px;
            border-bottom: 1px solid #ecf0f1;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }
        .article-admin-item:last-child {
            border-bottom: none;
        }
        .article-content {
            flex: 1;
        }
        .article-content h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .article-meta {
            color: #7f8c8d;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
        .article-text {
            color: #555;
            line-height: 1.6;
        }
        .article-actions {
            display: flex;
            gap: 10px;
            flex-shrink: 0;
        }
        .btn-edit {
            background: #f39c12;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9em;
            transition: background 0.3s ease;
        }
        .btn-edit:hover {
            background: #e67e22;
        }
        .btn-delete {
            background: #e74c3c;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9em;
            transition: background 0.3s ease;
        }
        .btn-delete:hover {
            background: #c0392b;
        }
        .no-articles {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-style: italic;
        }
        @media (max-width: 768px) {
            .article-admin-item {
                flex-direction: column;
                gap: 15px;
            }
            .article-actions {
                align-self: flex-start;
            }
            .admin-info {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="admin-header">
            <h1>Admin Panel</h1>
            <div class="admin-info">
                <div class="admin-welcome">
                    Vitajte, <strong><?php echo htmlspecialchars($admin['name']); ?></strong>!
                </div>
                <div class="admin-actions">
                    <a href="add.php" class="btn-add">+ Pridať článok</a>
                    <a href="logout.php" class="btn-logout">Odhlásiť sa</a>
                </div>
            </div>
        </div>

        <div class="articles-admin">
            <div class="articles-header">
                <h2>Správa článkov</h2>
            </div>
            
            <?php if ($message): ?>
                <div class="message success" style="margin: 20px;"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if (empty($clanky)): ?>
                <div class="no-articles">
                    <p>Zatiaľ nie sú pridané žiadne články.</p>
                    <a href="add.php" class="btn-add">Pridať prvý článok</a>
                </div>
            <?php else: ?>
                <?php foreach ($clanky as $clanok): ?>
                    <div class="article-admin-item">
                        <div class="article-content">
                            <h3><?php echo htmlspecialchars($clanok['title']); ?></h3>
                            <div class="article-meta">
                                Vytvorený: <?php echo date('d.m.Y H:i', strtotime($clanok['date_created'])); ?>
                                <?php if ($clanok['date_updated'] != $clanok['date_created']): ?>
                                    | Upravený: <?php echo date('d.m.Y H:i', strtotime($clanok['date_updated'])); ?>
                                <?php endif; ?>
                            </div>
                            <div class="article-text">
                                <?php echo nl2br(htmlspecialchars(substr($clanok['content'], 0, 200))); ?>
                                <?php if (strlen($clanok['content']) > 200): ?>...<?php endif; ?>
                            </div>
                        </div>
                        <div class="article-actions">
                            <a href="edit.php?id=<?php echo $clanok['id']; ?>" class="btn-edit">Upraviť</a>
                            <a href="delete.php?id=<?php echo $clanok['id']; ?>" class="btn-delete" 
                               onclick="return confirm('Naozaj chcete zmazať tento článok?')">Zmazať</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
