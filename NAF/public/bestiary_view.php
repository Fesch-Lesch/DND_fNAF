<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
checkAuth();

$pdo = getDBConnection();

// Получаем список всех существ из бестиария
$stmt = $pdo->query("SELECT * FROM BESTIARY ORDER BY challenge_rating, name");
$creatures = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Бестиарий - Просмотр</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .header { background: #333; color: white; padding: 20px; }
        .logout { float: right; background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; }
        .container { max-width: 1400px; margin: 30px auto; padding: 20px; }
        .nav-links { margin-bottom: 20px; }
        .nav-links a { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-right: 10px; display: inline-block; }
        .nav-links a:hover { background: #0056b3; }
        .bestiary-intro { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .creatures-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 20px; }
        .creature-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s; }
        .creature-card:hover { transform: translateY(-5px); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
        .creature-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; }
        .creature-header h3 { margin: 0 0 5px 0; font-size: 22px; }
        .creature-meta { font-size: 13px; opacity: 0.9; }
        .creature-body { padding: 20px; }
        .stats-row { display: grid; grid-template-columns: repeat(6, 1fr); gap: 8px; margin: 15px 0; }
        .stat-box { background: #f8f9fa; padding: 8px; border-radius: 4px; text-align: center; border: 1px solid #dee2e6; }
        .stat-label { font-size: 10px; color: #6c757d; font-weight: bold; }
        .stat-value { font-size: 16px; font-weight: bold; color: #333; }
        .info-row { margin: 10px 0; }
        .info-label { font-weight: bold; color: #495057; display: inline-block; width: 140px; }
        .info-value { color: #212529; }
        .section { margin: 15px 0; padding-top: 15px; border-top: 1px solid #dee2e6; }
        .section h4 { color: #495057; margin-bottom: 8px; font-size: 16px; }
        .section-content { background: #f8f9fa; padding: 12px; border-radius: 4px; font-size: 14px; line-height: 1.6; }
        .cr-badge { display: inline-block; background: #dc3545; color: white; padding: 5px 12px; border-radius: 12px; font-weight: bold; margin-left: 10px; }
        .no-creatures { text-align: center; padding: 60px; background: white; border-radius: 8px; }
        .combat-stats { background: #fff3cd; border: 1px solid #ffc107; padding: 12px; border-radius: 4px; margin: 10px 0; }
        .combat-stats strong { color: #856404; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🐉 Бестиарий D&D</h1>
        <a href="../logout.php" class="logout">Выход</a>
        <div style="clear: both;"></div>
        <p>Пользователь: <?= htmlspecialchars($_SESSION['username']) ?> (<?= $_SESSION['role'] ?>)</p>
    </div>
    
    <div class="container">
        <div class="nav-links">
            <?php if (isAdmin()): ?>
                <a href="../admin/dashboard.php">← Админ панель</a>
                <a href="../admin/bestiary.php">✏️ Редактировать бестиарий</a>
            <?php elseif (isTeacher()): ?>
                <a href="../teacher/dashboard.php">← Панель учителя</a>
            <?php elseif (isCaptain() && $_SESSION['team_id']): ?>
                <a href="team_stats.php?team_id=<?= $_SESSION['team_id'] ?>">← Моя команда</a>
            <?php endif; ?>
            <a href="team_ranking.php">🏆 Рейтинг команд</a>
            <a href="student_ranking.php">📊 Рейтинг студентов</a>
        </div>
        
        <div class="bestiary-intro">
            <h2>📖 Энциклопедия существ</h2>
            <p>Здесь собрана информация о различных существах, которые могут встретиться в мире D&D</p>
            <p><strong>Всего существ:</strong> <?= count($creatures) ?></p>
        </div>
        
        <?php if (count($creatures) > 0): ?>
        <div class="creatures-grid">
            <?php foreach ($creatures as $creature): ?>
            <div class="creature-card">
                <div class="creature-header">
                    <h3>
                        <?= htmlspecialchars($creature['name']) ?>
                        <span class="cr-badge">CR <?= $creature['challenge_rating'] ?></span>
                    </h3>
                    <div class="creature-meta">
                        <?= htmlspecialchars($creature['size']) ?> 
                        <?= htmlspecialchars($creature['type']) ?>, 
                        <?= htmlspecialchars($creature['alignment']) ?>
                    </div>
                </div>
                
                <div class="creature-body">
                    <div class="combat-stats">
                        <strong>⚔️ Боевые характеристики:</strong><br>
                        HP: <?= $creature['hp'] ?> | 
                        AC: <?= $creature['armor_class'] ?> | 
                        Скорость: <?= htmlspecialchars($creature['speed']) ?> | 
                        XP: <?= $creature['experience_points'] ?>
                    </div>
                    
                    <div class="stats-row">
                        <div class="stat-box">
                            <div class="stat-label">STR</div>
                            <div class="stat-value"><?= $creature['strength'] ?></div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">DEX</div>
                            <div class="stat-value"><?= $creature['dexterity'] ?></div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">CON</div>
                            <div class="stat-value"><?= $creature['constitution'] ?></div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">INT</div>
                            <div class="stat-value"><?= $creature['intelligence'] ?></div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">WIS</div>
                            <div class="stat-value"><?= $creature['wisdom'] ?></div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">CHA</div>
                            <div class="stat-value"><?= $creature['charisma'] ?></div>
                        </div>
                    </div>
                    
                    <?php if ($creature['damage_vulnerabilities']): ?>
                    <div class="info-row">
                        <span class="info-label">🔻 Уязвимости:</span>
                        <span class="info-value"><?= htmlspecialchars($creature['damage_vulnerabilities']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($creature['damage_resistances']): ?>
                    <div class="info-row">
                        <span class="info-label">🛡️ Сопротивление:</span>
                        <span class="info-value"><?= htmlspecialchars($creature['damage_resistances']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($creature['damage_immunities']): ?>
                    <div class="info-row">
                        <span class="info-label">🚫 Иммунитет (урон):</span>
                        <span class="info-value"><?= htmlspecialchars($creature['damage_immunities']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($creature['condition_immunities']): ?>
                    <div class="info-row">
                        <span class="info-label">✅ Иммунитет (состояния):</span>
                        <span class="info-value"><?= htmlspecialchars($creature['condition_immunities']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($creature['senses']): ?>
                    <div class="info-row">
                        <span class="info-label">👁️ Чувства:</span>
                        <span class="info-value"><?= htmlspecialchars($creature['senses']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($creature['languages']): ?>
                    <div class="info-row">
                        <span class="info-label">💬 Языки:</span>
                        <span class="info-value"><?= htmlspecialchars($creature['languages']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($creature['habitat']): ?>
                    <div class="info-row">
                        <span class="info-label">🌍 Среда обитания:</span>
                        <span class="info-value"><?= htmlspecialchars($creature['habitat']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($creature['description']): ?>
                    <div class="section">
                        <h4>📝 Описание</h4>
                        <div class="section-content"><?= nl2br(htmlspecialchars($creature['description'])) ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($creature['special_abilities']): ?>
                    <div class="section">
                        <h4>✨ Особые способности</h4>
                        <div class="section-content"><?= nl2br(htmlspecialchars($creature['special_abilities'])) ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($creature['actions']): ?>
                    <div class="section">
                        <h4>⚔️ Действия</h4>
                        <div class="section-content"><?= nl2br(htmlspecialchars($creature['actions'])) ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($creature['legendary_actions']): ?>
                    <div class="section">
                        <h4>👑 Легендарные действия</h4>
                        <div class="section-content"><?= nl2br(htmlspecialchars($creature['legendary_actions'])) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="no-creatures">
            <h3>🐉 Бестиарий пуст</h3>
            <p>В данный момент в бестиарии нет существ.</p>
            <?php if (isAdmin()): ?>
                <br>
                <a href="../admin/bestiary.php" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block;">
                    Добавить существ
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>