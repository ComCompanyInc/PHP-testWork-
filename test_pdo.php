<?php

    try {
        $pdo = new PDO(
            "pgsql:host=localhost;port=5432;dbname=blog_db",
            "postgres",
            "root",
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        echo "Подключение успешно!";

        // Проверка версии PostgreSQL
        $version = $pdo->query('SELECT version()')->fetchColumn();
        echo "\nВерсия PostgreSQL: " . $version;

    } catch (PDOException $e) {
        echo "Ошибка подключения: " . $e->getMessage();
    }