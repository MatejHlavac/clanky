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
    
    // Debug informácie
    error_log("POST data: " . print_r($_POST, true));
    error_log("Title: " . $title);
    error_log("Content length: " . strlen($content));
    
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
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/decoupled-document/ckeditor.js"></script>
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
        
        /* CKEditor štýly pre obrázky */
        .ck-editor__editable img {
            max-width: 100% !important;
            height: auto !important;
            display: block !important;
        }
        
        /* Obtiekanie obrázkov textom */
        .ck-editor__editable .image-inline {
            display: inline-block !important;
            margin: 0 10px !important;
        }
        
        .ck-editor__editable .image-side {
            float: right !important;
            margin: 0 0 10px 10px !important;
        }
        
        .ck-editor__editable .image-block {
            display: block !important;
            margin: 10px auto !important;
        }
        
        .ck-editor__editable {
            min-height: 500px;
        }
        
        /* Zvýšenie veľkosti editovacej plochy */
        .ck-editor {
            width: 100% !important;
        }
        
        .ck-editor__main {
            width: 100% !important;
        }
        
        .ck-editor__editable {
            width: 100% !important;
            min-height: 500px !important;
        }
        
        /* Zarovnanie na stred */
        .article-form {
            max-width: 100%;
            margin: 0 auto;
        }
        
        .form-group {
            width: 100%;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
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
            
            <!-- Debug informácie -->
            <?php if ($_POST): ?>
                <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; padding: 15px; margin: 20px 0; font-family: monospace; font-size: 12px;">
                    <strong>DEBUG INFO:</strong><br>
                    <strong>Title:</strong> <?php echo htmlspecialchars($title ?? ''); ?><br>
                    <strong>Content length:</strong> <?php echo strlen($content ?? ''); ?><br>
                    <strong>Content preview:</strong> <?php echo htmlspecialchars(substr($content ?? '', 0, 100)); ?>...
                </div>
            <?php endif; ?>

            <form method="POST" class="article-form">
                <div class="form-group">
                    <label for="title">Názov článku:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="content">Obsah článku:</label>
                    <div id="toolbar-container"></div>
                    <div id="image-upload-area" style="border: 2px dashed #ccc; padding: 20px; margin-bottom: 10px; text-align: center; background: #f9f9f9; border-radius: 4px;">
                        <p>Presuňte obrázky sem alebo kliknite na vybratie súborov</p>
                        <input type="file" id="image-input" multiple accept="image/*" style="display: none;">
                        <button type="button" onclick="document.getElementById('image-input').click()" class="btn btn-secondary">Vybrať obrázky</button>
                    </div>
                    <div id="content" name="content"><?php echo $content ?? ''; ?></div>
                    <textarea id="content-hidden" name="content" style="display: none;"><?php echo htmlspecialchars($content ?? ''); ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Pridať článok</button>
                    <button type="button" onclick="testForm()" class="btn btn-secondary">Test formulára</button>
                    <a href="dashboard.php" class="btn btn-secondary">Zrušiť</a>
                </div>
            </form>
        </main>
    </div>

    <script>
        let editor;
        
        // CKEditor konfigurácia
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof DecoupledEditor !== 'undefined') {
                DecoupledEditor
                    .create(document.querySelector('#content'), {
                        toolbar: [
                            'heading', '|', 
                            'bold', 'italic', 'underline', 'strikethrough', '|',
                            'fontSize', 'fontColor', 'fontBackgroundColor', '|',
                            'alignment', '|',
                            'bulletedList', 'numberedList', '|', 
                            'outdent', 'indent', '|', 
                            'blockQuote', 'insertTable', '|',
                            'link', 'imageUpload', '|',
                            'undo', 'redo'
                        ],
                        heading: {
                            options: [
                                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                            ]
                        },
                        fontSize: {
                            options: [9, 11, 13, 'default', 17, 19, 21]
                        },
                        alignment: {
                            options: ['left', 'center', 'right', 'justify']
                        },
                        image: {
                            toolbar: [
                                'imageTextAlternative', 
                                'imageStyle:inline', 
                                'imageStyle:block', 
                                'imageStyle:side',
                                '|',
                                'imageResize'
                            ],
                            resizeOptions: [
                                {
                                    name: 'imageResize:original',
                                    label: 'Original',
                                    value: null
                                },
                                {
                                    name: 'imageResize:25',
                                    label: '25%',
                                    value: '25'
                                },
                                {
                                    name: 'imageResize:50',
                                    label: '50%',
                                    value: '50'
                                },
                                {
                                    name: 'imageResize:75',
                                    label: '75%',
                                    value: '75'
                                }
                            ]
                        },
                        table: {
                            contentToolbar: [
                                'tableColumn', 'tableRow', 'mergeTableCells',
                                'tableProperties', 'tableCellProperties'
                            ]
                        }
                    })
                    .then(editorInstance => {
                        editor = editorInstance;
                        console.log('CKEditor Document Editor initialized successfully');
                        
                        // Pridať toolbar do kontajnera
                        document.querySelector('#toolbar-container').appendChild(editor.ui.view.toolbar.element);
                    })
                    .catch(error => {
                        console.error('CKEditor initialization failed:', error);
                    });
            }
            
            // Upload funkcionalita
            setupImageUpload();
        });
        
        function setupImageUpload() {
            const uploadArea = document.getElementById('image-upload-area');
            const fileInput = document.getElementById('image-input');
            
            // Drag & drop
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                uploadArea.style.background = '#e9ecef';
            });
            
            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                uploadArea.style.background = '#f9f9f9';
            });
            
            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                uploadArea.style.background = '#f9f9f9';
                handleFiles(e.dataTransfer.files);
            });
            
            // File input change
            fileInput.addEventListener('change', function(e) {
                handleFiles(e.target.files);
            });
        }
        
        function handleFiles(files) {
            for (let file of files) {
                if (file.type.startsWith('image/')) {
                    uploadImage(file);
                }
            }
        }
        
        function uploadImage(file) {
            console.log('Uploading file:', file.name);
            const formData = new FormData();
            formData.append('file', file);
            
            fetch('upload.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Upload response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Upload response data:', data);
                if (data.success) {
                    console.log('Upload successful, inserting image:', data.url);
                    insertImageToEditor(data.url);
                } else {
                    console.error('Upload error details:', data);
                    alert('Chyba pri uploadu: ' + data.message + (data.debug ? '\nDebug: ' + JSON.stringify(data.debug) : ''));
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                alert('Chyba pri uploadu obrázka: ' + error.message);
            });
        }
        
        function insertImageToEditor(imageUrl) {
            console.log('Inserting image to editor:', imageUrl);
            if (editor) {
                editor.model.change(writer => {
                    const imageElement = writer.createElement('imageBlock', {
                        src: imageUrl
                    });
                    console.log('Image element created:', imageElement);
                    editor.model.insertContent(imageElement);
                    console.log('Image inserted successfully');
                });
            } else {
                console.error('Editor not available');
            }
        }
        
        // Zabezpečenie, že sa CKEditor obsah pošle pri submit
        document.querySelector('form').addEventListener('submit', function(e) {
            console.log('Form submit event triggered');
            
            // Custom validácia
            const title = document.getElementById('title').value.trim();
            let content = '';
            
            if (editor) {
                console.log('Updating source element');
                // Aktualizuj skrytý textarea s obsahom z CKEditor
                content = editor.getData();
                document.getElementById('content-hidden').value = content;
            } else {
                content = document.getElementById('content').innerHTML.trim();
                document.getElementById('content-hidden').value = content;
            }
            
            // Validácia
            if (!title) {
                e.preventDefault();
                alert('Názov článku je povinný.');
                document.getElementById('title').focus();
                return false;
            }
            
            if (!content) {
                e.preventDefault();
                alert('Obsah článku je povinný.');
                if (editor) {
                    editor.focus();
                } else {
                    document.getElementById('content').focus();
                }
                return false;
            }
            
            console.log('Form validation passed');
        });
        
        // Test - skontroluj či sa formulár nachádza
        console.log('Form found:', document.querySelector('form'));
        
        // Test funkcia
        window.testForm = function() {
            console.log('Test button clicked');
            const title = document.getElementById('title').value.trim();
            let content = '';
            
            if (editor) {
                content = editor.getData();
                console.log('Editor content:', content);
            } else {
                content = document.getElementById('content').value.trim();
                console.log('Textarea content:', content);
            }
            
            console.log('Title:', title);
            console.log('Content length:', content.length);
            console.log('Editor available:', !!editor);
            
            alert(`Title: "${title}"\nContent length: ${content.length}\nEditor: ${!!editor}`);
        };
    </script>
</body>
</html>
