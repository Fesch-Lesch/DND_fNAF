<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
checkRole(['admin']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель - DND School System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .header { background: #333; color: white; padding: 20px; }
        .header h1 { display: inline-block; }
        .logout { float: right; background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 20px; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px; }
        .menu-item { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; }
        .menu-item h3 { margin-bottom: 15px; color: #333; }
        .menu-item a { display: inline-block; background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-top: 10px; }
        .menu-item a:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎲 Админ панель</h1>
        <a href="../logout.php" class="logout">Выход</a>
        <div style="clear: both;"></div>
        <p>Добро пожаловать, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
    </div>
    
    <div class="container">
        <h2>Управление системой</h2>
        
        <div class="menu-grid">
            <div class="menu-item">
                <h3>👥 Студенты</h3>
                <p>Управление студентами</p>
                <a href="students.php">Список студентов</a>
                <a href="add_student.php">Добавить студента</a>
                <a href="change_score.php">Изменить баллы</a>
            </div>
            
            <div class="menu-item">
                <h3>⚔️ Персонажи</h3>
                <p>Управление персонажами</p>
                <a href="characters.php">Список персонажей</a>
                <a href="add_character.php">Создать персонажа</a>
            </div>
            
            <div class="menu-item">
                <h3>🐉 Бестиарий</h3>
                <p>Управление существами</p>
                <a href="bestiary.php">Редактор бестиария</a>
            </div>
            
            <div class="menu-item">
                <h3>📊 Статистика</h3>
                <p>Просмотр статистики</p>
                <a href="../public/team_ranking.php">Рейтинг команд</a>
                <a href="../public/student_ranking.php">Рейтинг студентов</a>
            </div>
        </div>
    </div>
</body>
</html>