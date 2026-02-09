<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
checkRole(['admin']);

$pdo = getDBConnection();

// Получаем список персонажей
$stmt = $pdo->query("
    SELECT c.*, t.team_color, t.team_id
    FROM CHARACTERS c
    LEFT JOIN TEAMS t ON c.character_id = t.character_id
    ORDER BY c.character_id
");
$characters = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список персонажей</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .header { background: #333; color: white; padding: 20px; }
        .container { max-width: 1400px; margin: 30px auto; padding: 20px; background: white; border-radius: 8px; }
        .character-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; margin-top: 20px; }
        .character-card { border: 2px solid #007bff; border-radius: 8px; padding: 20px; background: #f8f9fa; }
        .character-header { background: #007bff; color: white; padding: 10px; margin: -20px -20px 15px -20px; border-radius: 6px 6px 0 0; }
        .character-header h3 { margin: 0; }
        .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin: 15px 0; }
        .stat { background: white; padding: 8px; border-radius: 4px; text-align: center; border: 1px solid #ddd; }
        .stat-label { font-size: 11px; color: #666; }
        .stat-value { font-size: 18px; font-weight: bold; color: #007bff; }
        .btn { padding: 8px 15px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 5px 5px 0 0; font-size: 14px; }
        .btn-edit { background: #ffc107; color: black; }
        .btn-view { background: #17a2b8; color: white; }
        .back { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; }
        .add-btn { background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; float: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>⚔️ Список персонажей</h1>
    </div>
    
    <div class="container">
        <a href="dashboard.php" class="back">← Назад к панели</a>
        <a href="add_character.php" class="add-btn">+ Создать персонажа</a>
        <div style="clear: both;"></div>
        
        <div class="character-grid">
            <?php foreach ($characters as $char): ?>
            <div class="character-card">
                <div class="character-header">
                    <h3><?= htmlspecialchars($char['name']) ?></h3>
                    <small><?= htmlspecialchars($char['race']) ?> • <?= htmlspecialchars($char['class']) ?> • Уровень <?= $char['level'] ?></small>
                </div>
                
                <p><strong>Команда:</strong> <?= htmlspecialchars($char['team_color'] ?? 'Не назначена') ?></p>
                
                <div class="stats">
                    <div class="stat">
                        <div class="stat-label">❤️ HP</div>
                        <div class="stat-value"><?= $char['hp'] ?></div>
                    </div>
                    <div class="stat">
                        <div class="stat-label">🛡️ Броня</div>
                        <div class="stat-value"><?= $char['armor'] ?></div>
                    </div>
                    <div class="stat">
                        <div class="stat-label">⚡ Инициатива</div>
                        <div class="stat-value"><?= $char['initiative'] ?></div>
                    </div>
                    <div class="stat">
                        <div class="stat-label">💪 Сила</div>
                        <div class="stat-value"><?= $char['strength'] ?></div>
                    </div>
                    <div class="stat">
                        <div class="stat-label">🤸 Ловкость</div>
                        <div class="stat-value"><?= $char['dexterity'] ?></div>
                    </div>
                    <div class="stat">
                        <div class="stat-label">🏋️ Телосложение</div>
                        <div class="stat-value"><?= $char['constitution'] ?></div>
                    </div>
                    <div class="stat">
                        <div class="stat-label">🧠 Интеллект</div>
                        <div class="stat-value"><?= $char['intelligence'] ?></div>
                    </div>
                    <div class="stat">
                        <div class="stat-label">🦉 Мудрость</div>
                        <div class="stat-value"><?= $char['wisdom'] ?></div>
                    </div>
                    <div class="stat">
                        <div class="stat-label">💬 Харизма</div>
                        <div class="stat-value"><?= $char['charisma'] ?></div>
                    </div>
                </div>
                
                <div>
                    <a href="edit_character.php?id=<?= $char['character_id'] ?>" class="btn btn-edit">Изменить</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>