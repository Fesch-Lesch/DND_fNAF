<?php
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'DnD System' ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <a href="/dashboard.php">DnD System</a>
        </div>
        <?php if (isLoggedIn()): ?>
        <ul class="nav-menu">
            <li><a href="/public/team-rating.php">Рейтинг команд</a></li>
            <li><a href="/public/student-rating.php">Рейтинг студентов</a></li>
            <li><a href="/public/team-stats.php">Статистика команды</a></li>
            <li><a href="/public/bestiary-view.php">Бестиарий</a></li>
            
            <?php if (isTeacher() || isAdmin()): ?>
            <li><a href="/teacher/score.php">Изменить баллы</a></li>
            <?php endif; ?>
            
            <?php if (isAdmin()): ?>
            <li class="dropdown">
                <a href="#">Админ ▼</a>
                <ul class="dropdown-menu">
                    <li><a href="/admin/students.php">Студенты</a></li>
                    <li><a href="/admin/characters.php">Персонажи</a></li>
                    <li><a href="/admin/bestiary.php">Редактор бестиария</a></li>
                </ul>
            </li>
            <?php endif; ?>
        </ul>
        <div class="nav-user">
            <span><?= htmlspecialchars($_SESSION['username']) ?> (<?= $_SESSION['role'] ?>)</span>
            <a href="/logout.php" class="btn btn-logout">Выход</a>
        </div>
        <?php endif; ?>
    </nav>
    <main class="container"></main>