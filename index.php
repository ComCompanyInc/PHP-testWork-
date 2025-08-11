<?php
require 'config.php';

$results = [];
$searchTerm = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search']) && strlen($_POST['search']) >= 3) {
    $searchTerm = trim($_POST['search']);

    try {
        $pdo = new PDO("pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("
            SELECT p.title, c.body 
            FROM posts p
            JOIN comments c ON p.id = c.post_id
            WHERE c.body LIKE :search
        ");
        $stmt->execute([':search' => "%$searchTerm%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        die("Ошибка подключения к БД: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Поиск по комментариям</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .search-form { margin-bottom: 20px; }
        .result-item { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .comment { background: #f5f5f5; padding: 10px; margin-top: 5px; }
    </style>
</head>
<body>
    <h1>Поиск записей по комментариям</h1>

    <form class="search-form" method="POST">
        <input type="text" name="search" placeholder="Введите минимум 3 символа..."
               value="<?= htmlspecialchars($searchTerm) ?>" required minlength="3">
        <button type="submit">Найти</button>
    </form>

    <?php if (!empty($results)): ?>
        <h2>Результаты поиска для "<?= htmlspecialchars($searchTerm) ?>"</h2>

        <?php foreach ($results as $result): ?>
            <div class="result-item">
                <h3><?= htmlspecialchars($result['title']) ?></h3>
                <div class="comment"><?= nl2br(htmlspecialchars($result['body'])) ?></div>
            </div>
        <?php endforeach; ?>

    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <p>Ничего не найдено.</p>
    <?php endif; ?>
</body>
</html>