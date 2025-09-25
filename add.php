<?php
// Stránka na pridávanie nových článkov
require_once 'config/database.php';
require_once 'includes/functions.php';

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
    <title>Pridať článok - Správa článkov</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Správa článkov</h1>
            <nav>
                <a href="index.php">Zoznam článkov</a>
                <a href="add.php" class="active">Pridať článok</a>
            </nav>
        </header>

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
                    <textarea id="content" name="content" rows="10" required><?php echo htmlspecialchars($content ?? ''); ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Pridať článok</button>
                    <a href="index.php" class="btn btn-secondary">Zrušiť</a>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
