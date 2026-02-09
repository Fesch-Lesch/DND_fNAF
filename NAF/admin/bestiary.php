<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
checkRole(['admin']);

$pdo = getDBConnection();
$success = '';
$error = '';

// Обработка добавления нового существа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    try {
        $stmt = $pdo->prepare("INSERT INTO BESTIARY (name, type, size, alignment, challenge_rating, experience_points, hp, armor_class, speed, strength, dexterity, constitution, intelligence, wisdom, charisma, damage_vulnerabilities, damage_resistances, damage_immunities, condition_immunities, senses, languages, special_abilities, actions, legendary_actions, description, habitat) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'], $_POST['type'], $_POST['size'], $_POST['alignment'], 
            (float)$_POST['challenge_rating'], (int)$_POST['experience_points'], 
            (int)$_POST['hp'], (int)$_POST['armor_class'], $_POST['speed'],
            (int)$_POST['strength'], (int)$_POST['dexterity'], (int)$_POST['constitution'],
            (int)$_POST['intelligence'], (int)$_POST['wisdom'], (int)$_POST['charisma'],
            $_POST['damage_vulnerabilities'] ?: null, $_POST['damage_resistances'] ?: null,
            $_POST['damage_immunities'] ?: null, $_POST['condition_immunities'] ?: null,
            $_POST['senses'] ?: null, $_POST['languages'] ?: null,
            $_POST['special_abilities'] ?: null, $_POST['actions'] ?: null,
            $_POST['legendary_actions'] ?: null, $_POST['description'] ?: null,
            $_POST['habitat'] ?: null
        ]);
        $success = 'Существо успешно добавлено в бестиарий!';
    } catch (PDOException $e) {
        $error = 'Ошибка: ' . $e->getMessage();
    }
}

// Обработка удаления существа
if (isset($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM BESTIARY WHERE creature_id = ?");
        $stmt->execute([(int)$_GET['delete']]);
        $success = 'Существо удалено из бестиария!';
    } catch (PDOException $e) {
        $error = 'Ошибка: ' . $e->getMessage();
    }
}

// Получаем список существ
$stmt = $pdo->query("SELECT * FROM BESTIARY ORDER BY challenge_rating, name");
$creatures = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Бестиарий - Редактор</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .header { background: #333; color: white; padding: 20px; }
        .container { max-width: 1400px; margin: 30px auto; padding: 20px; }
        .back { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .add-form { background: white; padding: 30px; border-radius: 8px; margin-bottom: 30px; }
        .form-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
        .form-group { margin-bottom: 15px; }
        .form-group.full-width { grid-column: 1 / -1; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; font-size: 13px; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        button { background: #28a745; color: white; padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #218838; }
        .creatures-list { background: white; padding: 20px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; font-size: 14px; }
        th { background: #dc3545; color: white; }
        tr:hover { background: #f5f5f5; }
        .btn-delete { background: #dc3545; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 12px; }
        .toggle-form { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-bottom: 20px; }
    </style>
    <script>
        function toggleForm() {
            const form = document.getElementById('add-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <div class="header">
        <h1>🐉 Бестиарий - Редактор</h1>
    </div>
    
    <div class="container">
        <a href="dashboard.php" class="back">← Назад к панели</a>
        
        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <button class="toggle-form" onclick="toggleForm()">➕ Добавить новое существо</button>
        
        <div id="add-form" class="add-form" style="display: none;">
            <h2>Добавить существо</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Название:</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Тип:</label>
                        <input type="text" name="type" placeholder="Например: Дракон, Нежить">
                    </div>
                    <div class="form-group">
                        <label>Размер:</label>
                        <select name="size">
                            <option value="Крошечный">Крошечный</option>
                            <option value="Маленький">Маленький</option>
                            <option value="Средний" selected>Средний</option>
                            <option value="Большой">Большой</option>
                            <option value="Огромный">Огромный</option>
                            <option value="Гигантский">Гигантский</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Мировоззрение:</label>
                        <input type="text" name="alignment" placeholder="Например: Нейтральное зло">
                    </div>
                    <div class="form-group">
                        <label>CR (Уровень опасности):</label>
                        <input type="number" name="challenge_rating" step="0.125" min="0" value="1">
                    </div>
                    <div class="form-group">
                        <label>Опыт:</label>
                        <input type="number" name="experience_points" min="0" value="200">
                    </div>
                    <div class="form-group">
                        <label>HP (Здоровье):</label>
                        <input type="number" name="hp" min="1" value="10" required>
                    </div>
                    <div class="form-group">
                        <label>AC (Класс брони):</label>
                        <input type="number" name="armor_class" min="1" value="10" required>
                    </div>
                    <div class="form-group">
                        <label>Скорость:</label>
                        <input type="text" name="speed" value="30 фт" placeholder="Например: 30 фт, полет 60 фт">
                    </div>
                    <div class="form-group">
                        <label>Сила (STR):</label>
                        <input type="number" name="strength" min="1" max="30" value="10">
                    </div>
                    <div class="form-group">
                        <label>Ловкость (DEX):</label>
                        <input type="number" name="dexterity" min="1" max="30" value="10">
                    </div>
                    <div class="form-group">
                        <label>Телосложение (CON):</label>
                        <input type="number" name="constitution" min="1" max="30" value="10">
                    </div>
                    <div class="form-group">
                        <label>Интеллект (INT):</label>
                        <input type="number" name="intelligence" min="1" max="30" value="10">
                    </div>
                    <div class="form-group">
                        <label>Мудрость (WIS):</label>
                        <input type="number" name="wisdom" min="1" max="30" value="10">
                    </div>
                    <div class="form-group">
                        <label>Харизма (CHA):</label>
                        <input type="number" name="charisma" min="1" max="30" value="10">
                    </div>
                    <div class="form-group full-width">
                        <label>Уязвимости к урону:</label>
                        <input type="text" name="damage_vulnerabilities" placeholder="Например: огонь, холод">
                    </div>
                    <div class="form-group full-width">
                        <label>Сопротивление к урону:</label>
                        <input type="text" name="damage_resistances" placeholder="Например: дробящий, колющий">
                    </div>
                    <div class="form-group full-width">
                        <label>Иммунитет к урону:</label>
                        <input type="text" name="damage_immunities" placeholder="Например: яд, психический">
                    </div>
                    <div class="form-group full-width">
                        <label>Иммунитет к состояниям:</label>
                        <input type="text" name="condition_immunities" placeholder="Например: очарован, испуган">
                    </div>
                    <div class="form-group">
                        <label>Чувства:</label>
                        <input type="text" name="senses" placeholder="Например: темное зрение 60 фт">
                    </div>
                    <div class="form-group">
                        <label>Языки:</label>
                        <input type="text" name="languages" placeholder="Например: Общий, Драконий">
                    </div>
                    <div class="form-group">
                        <label>Среда обитания:</label>
                        <input type="text" name="habitat" placeholder="Например: Подземелья, леса">
                    </div>
                    <div class="form-group full-width">
                        <label>Особые способности:</label>
                        <textarea name="special_abilities" rows="3" placeholder="Опишите особые способности..."></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label>Действия:</label>
                        <textarea name="actions" rows="3" placeholder="Опишите доступные действия..." required></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label>Легендарные действия:</label>
                        <textarea name="legendary_actions" rows="3" placeholder="Опишите легендарные действия (если есть)..."></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label>Описание:</label>
                        <textarea name="description" rows="4" placeholder="Общее описание существа..."></textarea>
                    </div>
                    <div class="form-group full-width">
                        <button type="submit">Добавить существо в бестиарий</button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="creatures-list">
            <h2>Список существ в бестиарии</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Тип</th>
                        <th>Размер</th>
                        <th>CR</th>
                        <th>HP</th>
                        <th>AC</th>
                        <th>Опыт</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($creatures) > 0): ?>
                        <?php foreach ($creatures as $creature): ?>
                        <tr>
                            <td><?= $creature['creature_id'] ?></td>
                            <td><?= htmlspecialchars($creature['name']) ?></td>
                            <td><?= htmlspecialchars($creature['type']) ?></td>
                            <td><?= htmlspecialchars($creature['size']) ?></td>
                            <td><?= $creature['challenge_rating'] ?></td>
                            <td><?= $creature['hp'] ?></td>
                            <td><?= $creature['armor_class'] ?></td>
                            <td><?= $creature['experience_points'] ?> XP</td>
                            <td>
                                <a href="?delete=<?= $creature['creature_id'] ?>" class="btn-delete" onclick="return confirm('Вы уверены?')">Удалить</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align: center;">Бестиарий пуст. Добавьте существ!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>