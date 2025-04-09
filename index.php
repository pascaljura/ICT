<?php
// Cílová složka dle URL parametru
$relativePath = isset($_GET['dir']) ? urldecode($_GET['dir']) : '';
$targetDir = realpath(__DIR__ . DIRECTORY_SEPARATOR . $relativePath);

// Bezpečnostní kontrola: zabránit přístupu mimo základní adresář
if (strpos($targetDir, realpath(__DIR__)) !== 0) {
    $targetDir = __DIR__;
    $relativePath = '';
}

$items = array_diff(scandir($targetDir), array('..', '.', 'index.php'));

// Cesta k rodičovské složce
$parentDir = dirname($relativePath);
$isBaseDir = $relativePath === '' || $targetDir === realpath(__DIR__);

// Vytvoření breadcrumbů
$breadcrumbs = [];
$crumbPath = '';
if ($relativePath !== '') {
    $parts = explode(DIRECTORY_SEPARATOR, $relativePath);
    foreach ($parts as $index => $part) {
        $crumbPath .= ($index > 0 ? DIRECTORY_SEPARATOR : '') . $part;
        $breadcrumbs[] = ['name' => $part, 'path' => $crumbPath];
    }
}
?>

<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seznam souborů a složek</title>
    <link rel="icon" type="image/x-icon" href="assets/img/icon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/865012b7e6.js" crossorigin="anonymous"></script>
    <style>
        :root {
            color-scheme: light dark;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        canvas#canvas {
            position: fixed;
            top: 0;
            left: 0;
            z-index: -1;
            width: 100%;
            height: 100%;
            display: block;
            pointer-events: none;
        }

        body {
            position: relative;
            color: var(--bs-body-color);
        }

        .container {
            position: relative;
            z-index: 1;
        }

        .file-list {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: var(--bs-body-bg);
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .file-item i {
            margin-right: 10px;
            transition: transform 0.2s;
        }

        a {
            transition: color 0.2s;
        }

        .file-item:hover i {
            transform: scale(1.2);
        }

        a:hover {
            color: #0d6efd;
        }

        .file-meta {
            font-size: 0.9em;
            color: gray;
        }

        .file-meta div span {
            font-weight: bold;
        }

        .file-list ul.list-group {
            max-height: 600px;
            overflow-y: auto;
        }

        .dark-mode-toggle {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            z-index: 999;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: var(--bs-body-bg);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .dark-mode-toggle img {
            height: 40px;
        }

        .dark-mode-toggle h1 {
            font-size: 1.2rem;
            margin: 0 15px;
        }

        .theme-icon {
            font-size: 1.2em;
        }

        footer {
            text-align: center;
            padding: 20px;
            font-size: 0.9em;
            color: gray;
            margin: 50px auto;
            background-color: var(--bs-body-bg);
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(0, 0, 0, 0.1);
            max-width: 800px;
            border-radius: 10px;
        }
    </style>
</head>

<body data-bs-theme="auto">
    <input type="hidden" value="100" step="1" min="1" id="circle_count">
    <input type="hidden" value="0.2" step="0.1" min="0" id="circle_speed">
    <input type="hidden" value="4" step="0.5" min="0.1" id="circle_radius">
    <input type="hidden" value="100" step="1" min="0.1" id="circle_connect_radius">
    <input type="hidden" id="circle_color">
    <input type="hidden" id="circle_connect_color">
    <input type="hidden" value="300" step="1" min="0" id="circle_mouse_repel_distance">
    <input type="hidden" value="0.99" step="0.01" min="0.1" id="circle_mouse_repel_friction">
    <canvas id="canvas"></canvas>

    <div class="container">
        <div class="dark-mode-toggle">
            <div class="d-flex align-items-center">
                <img src="assets/img/icon.ico" alt="Logo">
                <h1 class="ms-3">Seznam souborů a složek</h1>
            </div>
            <button class="btn btn-outline-secondary d-flex align-items-center" id="toggleTheme">
                <i class="fas fa-sun theme-icon" id="themeIcon"></i>
                <span class="ms-2">Přepnout téma</span>
            </button>
        </div>
        <div class="file-list">
            <?php if (!$isBaseDir): ?>
                <div class="mb-3">
                    <a href="?dir=<?php echo urlencode($parentDir); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Zpět
                    </a>
                </div>
            <?php endif; ?>

            <?php if (!empty($breadcrumbs)): ?>
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Domů</a></li>
                        <?php foreach ($breadcrumbs as $crumb): ?>
                            <li class="breadcrumb-item">
                                <a href="?dir=<?php echo urlencode($crumb['path']); ?>">
                                    <?php echo htmlspecialchars($crumb['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </nav>
            <?php endif; ?>

            <ul class="list-group">
                <?php foreach ($items as $item):
                    $path = $targetDir . DIRECTORY_SEPARATOR . $item;
                    $isDir = is_dir($path);
                    $size = !$isDir ? filesize($path) : 0;
                    $modified = date("d.m.Y H:i:s", filemtime($path));
                    $itemRelativePath = trim($relativePath . DIRECTORY_SEPARATOR . $item, DIRECTORY_SEPARATOR);
                    $link = $isDir
                        ? htmlspecialchars($_SERVER['PHP_SELF']) . '?dir=' . rawurlencode($itemRelativePath)
                        : htmlspecialchars(rawurlencode($item));
                    ?>
                    <li class="list-group-item file-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas <?php echo $isDir ? 'fa-folder' : 'fa-file'; ?>"></i>
                            <a href="<?php echo $link; ?>" class="text-decoration-none" <?php echo $isDir ? '' : 'target="_blank"'; ?>>
                                <?php echo htmlspecialchars($item); ?>
                            </a>
                        </div>
                        <div class="file-meta text-end">
                            <div>Upraveno: <span><?php echo $modified; ?></span></div>
                            <?php if (!$isDir): ?>
                                <div>Velikost: <span><?php echo number_format($size / 1024, 2); ?> KB</span></div>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <footer>
        Vytvořil Jiří Boucník | <a href="https://alba-rosa.cz" target="_blank">alba-rosa.cz</a>
    </footer>
    <script src="assets/js/circlePlayground.js"></script>
    <script>
        const toggleButton = document.getElementById('toggleTheme');
        const themeIcon = document.getElementById('themeIcon');
        const savedTheme = localStorage.getItem('theme');

        function setTheme(theme) {
            document.body.setAttribute('data-bs-theme', theme);
            localStorage.setItem('theme', theme);
            themeIcon.className = `fas theme-icon fa-${theme === 'dark' ? 'moon' : 'sun'}`;

            // dynamická změna barev canvasu
            document.getElementById('circle_color').value = theme === 'dark' ? '#ffffff' : '#292929';
            document.getElementById('circle_connect_color').value = theme === 'dark' ? '#888888' : '#000000';
        }

        if (savedTheme) {
            setTheme(savedTheme);
        } else {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            setTheme(prefersDark ? 'dark' : 'light');
        }

        toggleButton.addEventListener('click', () => {
            const currentTheme = document.body.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
        });
    </script>
</body>

</html>