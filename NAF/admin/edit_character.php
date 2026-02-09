<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
checkRole(['admin']);

$pdo = getDBConnection();
$success = '';
$error = '';

$character_id = (int)$_GET['id'];

// Получаем данные персонажа
$stmt = $pdo->prepare("SELECT * FROM CHARACTERS WHERE character_id = ?");
$stmt->execute([$character_id]);
$character = $stmt->fetch();

if (!$character) {
    die("Персонаж не найден");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("UPDATE CHARACTERS SET name = ?, race = ?, class = ?, level = ?, hp = ?, armor = ?, strength = ?, dexterity = ?, constitution = ?, intelligence = ?, wisdom = ?, charisma = ?, ability1 = ?, ability2 = ?, ability3 = ?, item1 = ?, item2 = ?, item3 = ?, initiative = ?, speed = ? WHERE character_id = ?");
        $stmt->execute([
            $_POST['name'], $_POST['race'], $_POST['class'], (int)$_POST['level'], 
            (int)$_POST['hp'], (int)$_POST['armor'], (int)$_POST['strength'], 
            (int)$_POST['dexterity'], (int)$_POST['constitution'], (int)$_POST['intelligence'], 
            (int)$_POST['wisdom'], (int)$_POST['charisma'], 
            $_POST['ability1'] ?: null, $_POST['ability2'] ?: null, $_POST['ability3'] ?: null,
            $_POST['item1'] ?: null, $_POST['item2'] ?: null, $_POST['item3'] ?: null,
            (int)$_POST['initiative'], (int)$_POST['speed'], $character_id
        ]);
        $success = 'Персонаж успешно обновлен!';
        
        // Обновляем данные для отображения
        $stmt = $pdo->prepare("SELECT * FROM CHARACTERS WHERE character_id = ?");
        $stmt->execute([$character_id]);
        $character = $stmt->fetch();
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
    <title>Редактировать персонажа</title>
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
        button { background: #007bff; color: white; padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #0056b3; }
        .back { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .section-title { grid-column: 1 / -1; background: #007bff; color: white; padding: 10px; margin: 10px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>✏️ Редактировать персонажа</h1>
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
            <div class="form-grid"></div>
                            <h3 class="section-title">Основная информация</h3>
                
                <div class="form-group">
                    <label>Имя персонажа:</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($character['name']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Раса:</label>
                    <input type="text" name="race" value="<?= htmlspecialchars($character['race']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Класс:</label>
                    <input type="text" name="class" value="<?= htmlspecialchars($character['class']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Уровень:</label>
                    <input type="number" name="level" value="<?= $character['level'] ?>" min="1" max="20" required>
                </div>
                
                <h3 class="section-title">Характеристики</h3>
                
                <div class="form-group">
                    <label>❤️ Здоровье (HP):</label>
                    <input type="number" name="hp" value="<?= $character['hp'] ?>" min="1" required>
                </div>
                
                <div class="form-group">
                    <label>🛡️ Броня:</label>
                    <input type="number" name="armor" value="<?= $character['armor'] ?>" min="0" required>
                </div>
                
                <div class="form-group">
                    <label>💪 Сила:</label>
                    <input type="number" name="strength" value="<?= $character['strength'] ?>" min="1" max="20" required>
                </div>
                
                <div class="form-group">
                    <label>🤸 Ловкость:</label>
                    <input type="number" name="dexterity" value="<?= $character['dexterity'] ?>" min="1" max="20" required>
                </div>
                
                <div class="form-group">
                    <label>🏋️ Телосложение:</label>
                    <input type="number" name="constitution" value="<?= $character['constitution'] ?>" min="1" max="20" required>
                </div>
                
                <div class="form-group">
                    <label>🧠 Интеллект:</label>
                    <input type="number" name="intelligence" value="<?= $character['intelligence'] ?>" min="1" max="20" required>
                </div>
                
                <div class="form-group">
                    <label>🦉 Мудрость:</label>
                    <input type="number" name="wisdom" value="<?= $character['wisdom'] ?>" min="1" max="20" required>
                </div>
                
                <div class="form-group">
                    <label>💬 Харизма:</label>
                    <input type="number" name="charisma" value="<?= $character['charisma'] ?>" min="1" max="20" required>
                </div>
                
                <div class="form-group">
                    <label>⚡ Инициатива:</label>
                    <input type="number" name="initiative" value="<?= $character['initiative'] ?>" min="0" required>
                </div>
                
                <div class="form-group">
                    <label>🏃 Скорость:</label>
                    <input type="number" name="speed" value="<?= $character['speed'] ?>" min="0" required>
                </div>
                
                <h3 class="section-title">Способности</h3>
                
                <div class="form-group full-width">
                    <label>Способность 1:</label>
                    <textarea name="ability1" rows="2"><?= htmlspecialchars($character['ability1'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group full-width">
                    <label>Способность 2:</label>
                    <textarea name="ability2" rows="2"><?= htmlspecialchars($character['ability2'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group full-width">
                    <label>Способность 3:</label>
                    <textarea name="ability3" rows="2"><?= htmlspecialchars($character['ability3'] ?? '') ?></textarea>
                </div>
                
                <h3 class="section-title">Предметы</h3>
                
                <div class="form-group full-width">
                    <label>Предмет 1:</label>
                    <textarea name="item1" rows="2"><?= htmlspecialchars($character['item1'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group full-width">
                    <label>Предмет 2:</label>
                    <textarea name="item2" rows="2"><?= htmlspecialchars($character['item2'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group full-width">
                    <label>Предмет 3:</label>
                    <textarea name="item3" rows="2"><?= htmlspecialchars($character['item3'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group full-width">
                    <button type="submit">Сохранить изменения</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>