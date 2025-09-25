<?php
// Hlavná stránka - zoznam článkov
require_once 'config/database.php';
require_once 'includes/functions.php';

$clanky = getClanky();
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Správa článkov</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Správa článkov</h1>
            <nav>
                <a href="index.php" class="active">Zoznam článkov</a>
                <a href="add.php">Pridať článok</a>
            </nav>
        </header>

        <main>
            <h2>Všetky články</h2>
            
            <?php if (empty($clanky)): ?>
                <p class="no-articles">Zatiaľ nie sú pridané žiadne články. <a href="add.php">Pridať prvý článok</a></p>
            <?php else: ?>
                <div class="articles-list">
                    <?php foreach ($clanky as $clanok): ?>
                        <article class="article-item">
                            <h3><?php echo htmlspecialchars($clanok['title']); ?></h3>
                            <p class="article-date"><?php echo date('d.m.Y H:i', strtotime($clanok['date_created'])); ?></p>
                            <div class="article-content">
                                <?php echo nl2br(htmlspecialchars($clanok['content'])); ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
