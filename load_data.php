<?php
require 'config.php';

try {
    // создаем подключение к бд (по тому файлику с конфигом, config.php)
    $pdo = new PDO("pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // перебираем джсонку для пользователей
    $postsData = file_get_contents('https://jsonplaceholder.typicode.com/posts');
    $posts = json_decode($postsData, true);

    $postCount = 0;
    $stmtPost = $pdo->prepare("INSERT INTO posts (id, user_id, title, body) VALUES (?, ?, ?, ?)");

    foreach ($posts as $post) {
        $stmtPost->execute([$post['id'], $post['userId'], $post['title'], $post['body']]);
        $postCount++;
    }

    // И аналогично для комментариев
    $commentsData = file_get_contents('https://jsonplaceholder.typicode.com/comments');
    $comments = json_decode($commentsData, true);

    $commentCount = 0;
    $stmtComment = $pdo->prepare("INSERT INTO comments (id, post_id, name, email, body) VALUES (?, ?, ?, ?, ?)");

    foreach ($comments as $comment) {
        $stmtComment->execute([$comment['id'], $comment['postId'], $comment['name'], $comment['email'], $comment['body']]);
        $commentCount++;
    }

    echo "Загружено $postCount записей и $commentCount комментариев\n";

} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}