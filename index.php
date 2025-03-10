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
</head>

<body>
    <h1>Seznam souborů ve složce</h1>
    <ul>
        <?php foreach ($files as $file): ?>
            <li><A HREF="./<?php echo htmlspecialchars($file); ?>"
                    target="_blank"><?php echo htmlspecialchars($file); ?></A></li>
        <?php endforeach; ?>
    </ul>
</body>

</html>