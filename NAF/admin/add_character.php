<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
checkRole(['admin']);

$pdo = getDBConnection();
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("INSERT INTO CHARACTERS (name, race, class, level, hp, armor, strength, dexterity, constitution, intelligence, wisdom, charisma, ability1, ability2, ability3, item1, item2, item3, initiative, speed) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'], $_POST['race'], $_POST['class'], (int)$_POST['level'], 
            (int)$_POST['hp'], (int)$_POST['armor'], (int)$_POST['strength'], 
            (int)$_POST['dexterity'], (int)$_POST['constitution'], (int)$_POST['intelligence'], 
            (int)$_POST['wisdom'], (int)$_POST['charisma'], 
            $_POST['ability1'] ?: null, $_POST['ability2'] ?: null, $_POST['ability3'] ?: null,
            $_POST['item1'] ?: null, $_POST['item2'] ?: null, $_POST['item3'] ?: null,
            (int)$_POST['initiative'], (int)$_POST['speed']
        ]);
        $success = 'Персонаж успешно создан!';
    } catch (PDOException $e) {
        $error = 'Ошибка: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создать персонажа</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .header { background: #333; color: white; padding: 20px; }
        .container { max-width: 1000px; margin: 30px auto; padding: 30px; background: white; border-radius: 8px; }
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group.full-width { grid-column: 1 / -1; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        button { background: #28a745; color: white; padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #218838; }
        .back { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .section-title { grid-column: 1 / -1; background: #007bff; color: white; padding: 10px; margin: 10px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>⚔️ Создать нового персонажа</h1>
    </div>
    
    <div class="container">
        <a href="characters.php" class="back">← Назад к списку</a>
        
        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-grid">
                <h3 class="section-title">Основная информация</h3>
                
                <div class="form-group">
                    <label>Имя персонажа:</label>
                    <input type="text" name="name" required>
                </div>
                
                <div class="form-group">
                    <label>Раса:</label>
                    <input type="text" name="race" required>
                </div>
                
                <div class="form-group">
                    <label>Класс:</label>
                    <input type="text" name="class" required>
                </div>
                
                <div class="form-group">
                    <label>Уровень:</label>
                    <input type="number" name="level" value="1" min="1" max="20" required>
                </div>
                
                <h3 class="section-title">Характеристики</h3>
                
                <div class="form-group">
                    <label>❤️ Здоровье (HP):</label>
                    <input type="number" name="hp" value="10" min="1" required>
                </div>
                
                <div class="form-group">
                    <label>🛡️ Броня:</label>
                    <input type="number" name="armor" value="10" min="0" required>
                </div>
                
                <div class="form-group">
                    <label>💪 Сила:</label>
                    <input type="number" name="strength" value="10" min="1" max="20" required>
                </div>
                
                <div class="form-group">
                    <label>🤸 Ловкость:</label>
                    <input type="number" name="dexterity" value="10" min="1" max="20" required>
                </div>
                
                <div class="form-group">
                    <label>🏋️ Телосложение:</label>
                    <input type="number" name="constitution" value="10" min="1" max="20" required>
                </div>
                
                <div class="form-group">
                    <label>🧠 Интеллект:</label>
                    <input type="number" name="intelligence" value="10" min="1" max="20" required>
                </div>
                
                <div class="form-group">
                    <label>🦉 Мудрость:</label>
                    <input type="number" name="wisdom" value="10" min="1" max="20" required>
                </div>
                
                <div class="form-group">
                    <label>💬 Харизма:</label>
                    <input type="number" name="charisma" value="10" min="1" max="20" required>
                </div>
                
                <div class="form-group">
                    <label>⚡ Инициатива:</label>
                    <input type="number" name="initiative" value="0" min="0" required>
                </div>
                
                <div class="form-group">
                    <label>🏃 Скорость:</label>
                    <input type="number" name="speed" value="30" min="0" required>
                </div>
                
                <h3 class="section-title">Способности</h3>
                
                <div class="form-group full-width">
                    <label>Способность 1:</label>
                    <textarea name="ability1" rows="2" placeholder="Описание первой способности..."></textarea>
                </div>
                
                <div class="form-group full-width">
                    <label>Способность 2:</label>
                    <textarea name="ability2" rows="2" placeholder="Описание второй способности..."></textarea>
                </div>
                
                <div class="form-group full-width">
                    <label>Способность 3:</label>
                    <textarea name="ability3" rows="2" placeholder="Описание третьей способности..."></textarea>
                </div>
                
                <h3 class="section-title">Предметы</h3>
                
                <div class="form-group full-width">
                    <label>Предмет 1:</label>
                    <textarea name="item1" rows="2" placeholder="Описание первого предмета..."></textarea>
                </div>
                
                <div class="form-group full-width">
                    <label>Предмет 2:</label>
                    <textarea name="item2" rows="2" placeholder="Описание второго предмета..."></textarea>
                </div>
                
                <div class="form-group full-width">
                    <label>Предмет 3:</label>
                    <textarea name="item3" rows="2" placeholder="Описание третьего предмета..."></textarea>
                </div>
                
                <div class="form-group full-width">
                    <button type="submit">Создать персонажа</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>