<?php
// Získání seznamu souborů v aktuální složce
$files = array_diff(scandir(__DIR__), array('..', '.'));
?>

<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seznam souborů</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .file-list {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .file-item i {
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="file-list">
            <h2 class="text-center mb-4">Seznam souborů</h2>
            <ul class="list-group">
                <?php foreach ($files as $file): ?>
                    <li class="list-group-item file-item">
                        <i class="fas fa-file"></i>
                        <a href="./<?php echo htmlspecialchars($file); ?>" target="_blank" class="text-decoration-none">
                            <?php echo htmlspecialchars($file); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</body>

</html>