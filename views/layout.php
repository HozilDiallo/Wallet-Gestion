<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Gestion de Portefeuilles') ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Gestion de Portefeuilles</h1>
            <p>Créez des portefeuilles, effectuez des dépôts et retraits, consultez l'historique des transactions.</p>
        </header>

        <?php if (!empty($flash)): ?>
            <div class="flash flash-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <main>
            <?= $content ?>
        </main>

        <footer>
            Application de gestion de portefeuilles — PHP &amp; SQLite
        </footer>
    </div>
</body>
</html>
