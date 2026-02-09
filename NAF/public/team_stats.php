<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
checkAuth();

$pdo = getDBConnection();

// Получаем ID команды
$team_id = isset($_GET['team_id']) ? (int)$_GET['team_id'] : null;

// Если пользователь - капитан, показываем только его команду
if ($_SESSION['role'] === 'captain' && $_SESSION['team_id']) {
    $team_id = $_SESSION['team_id'];
}

// Получаем список всех команд для выбора
$stmt = $pdo->query("SELECT team_id, team_color FROM TEAMS ORDER BY team_color");
$all_teams = $stmt->fetchAll();

$team = null;
$students = [];
$character = null;

if ($team_id) {
    // Получаем информацию о команде
    $stmt = $pdo->prepare("
        SELECT t.*, c.name as character_name, c.race, c.class, c.level, 
               c.hp, c.armor, c.strength, c.dexterity, c.constitution, 
               c.intelligence, c.wisdom, c.charisma, c.initiative, c.speed,
               c.ability1, c.ability2, c.ability3, c.item1, c.item2, c.item3
        FROM TEAMS t
        LEFT JOIN CHARACTERS c ON t.character_id = c.character_id
        WHERE t.team_id = ?
    ");
    $stmt->execute([$team_id]);
    $team = $stmt->fetch();
    
    if ($team) {
        // Получаем список студентов команды
        $stmt = $pdo->prepare("
            SELECT student_id, first_name, last_name, middle_name, score
            FROM STUDENTS
            WHERE team_id = ?
            ORDER BY score DESC, last_name
        ");
        $stmt->execute([$team_id]);
        $students = $stmt->fetchAll();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Статистика команды</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .header { background: #333; color: white; padding: 20px; }
        .logout { float: right; background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 20px; }
        .team-selector { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .team-selector select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; }
        .team-info { background: white; padding: 30px; border-radius: 8px; margin-bottom: 20px; }
        .team-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; margin: -30px -30px 20px -30px; border-radius: 8px 8px 0 0; }
        .team-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .stat-box { background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center; border: 2px solid #e9ecef; }
        .stat-label { font-size: 14px; color: #6c757d; margin-bottom: 5px; }
        .stat-value { font-size: 32px; font-weight: bold; color: #007bff; }
        .character-card { background: #fff3cd; border: 2px solid #ffc107; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .character-stats { display: grid; grid-template-columns: repeat(6, 1fr); gap: 10px; margin: 15px 0; }
        .char-stat { background: white; padding: 10px; border-radius: 4px; text-align: center; border: 1px solid #ddd; }
        .char-stat-label { font-size: 11px; color: #666; }
        .char-stat-value { font-size: 20px; font-weight: bold; color: #333; }
        .students-list { background: white; padding: 20px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #007bff; color: white; }
        tr:hover { background: #f5f5f5; }
        .back { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin-bottom: 20px; }
        .abilities, .items { margin: 15px 0; }
        .ability-item, .item-item { background: white; padding: 10px; border-left: 4px solid #28a745; margin-bottom: 10px; border-radius: 4px; }
        .no-data { text-align: center; padding: 40px; color: #6c757d; }
    </style>
    <script>
        function changeTeam() {
            const select = document.getElementById('team_select');
            if (select.value) {
                window.location.href = 'team_stats.php?team_id=' + select.value;
            }
        }
    </script>
</head>
<body>
    <div class="header">
        <h1>🏆 Статистика команды</h1>
        <a href="../logout.php" class="logout">Выход</a>
        <div style="clear: both;"></div>
        <p>Пользователь: <?= htmlspecialchars($_SESSION['username']) ?> (<?= $_SESSION['role'] ?>)</p>
    </div>
    
    <div class="container">
        <?php if ($_SESSION['role'] !== 'captain'): ?>
            <a href="team_ranking.php" class="back">← К рейтингу команд</a>
        <?php endif; ?>
        
        <?php if ($_SESSION['role'] !== 'captain'): ?>
        <div class="team-selector">
            <label><strong>Выберите команду:</strong></label>
            <select id="team_select" onchange="changeTeam()">
                <option value="">-- Выберите команду --</option>
                <?php foreach ($all_teams as $t): ?>
                    <option value="<?= $t['team_id'] ?>" <?= $team_id == $t['team_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['team_color']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        
        <?php if ($team): ?>
        <div class="team-info">
            <div class="team-header">
                <h2>Команда: <?= htmlspecialchars($team['team_color']) ?></h2>
            </div>
            
            <div class="team-stats">
                <div class="stat-box">
                    <div class="stat-label">💰 Общие баллы</div>
                    <div class="stat-value"><?= $team['amount'] ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">✨ Вдохновение</div>
                    <div class="stat-value"><?= $team['inspiration'] ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">👥 Членов команды</div>
                    <div class="stat-value"><?= count($students) ?></div>
                </div>
            </div>
            
            <?php if ($team['character_name']): ?>
            <div class="character-card">
                <h3>⚔️ Персонаж команды: <?= htmlspecialchars($team['character_name']) ?></h3>
                <p><strong>Раса:</strong> <?= htmlspecialchars($team['race']) ?> | 
                   <strong>Класс:</strong> <?= htmlspecialchars($team['class']) ?> | 
                   <strong>Уровень:</strong> <?= $team['level'] ?></p>
                
                <div class="character-stats">
                    <div class="char-stat">
                        <div class="char-stat-label">❤️ HP</div>
                        <div class="char-stat-value"><?= $team['hp'] ?></div>
                    </div>
                    <div class="char-stat">
                        <div class="char-stat-label">🛡️ Броня</div>
                        <div class="char-stat-value"><?= $team['armor'] ?></div>
                    </div>
                    <div class="char-stat">
                        <div class="char-stat-label">💪 Сила</div>
                        <div class="char-stat-value"><?= $team['strength'] ?></div>
                    </div>
                    <div class="char-stat">
                        <div class="char-stat-label">🤸 Ловкость</div>
                        <div class="char-stat-value"><?= $team['dexterity'] ?></div>
                    </div>
                    <div class="char-stat">
                        <div class="char-stat-label">🏋️ Телосл.</div>
                        <div class="char-stat-value"><?= $team['constitution'] ?></div>
                    </div>
                    <div class="char-stat">
                        <div class="char-stat-label">🧠 Интелл.</div>
                        <div class="char-stat-value"><?= $team['intelligence'] ?></div>
                    </div>
                    <div class="char-stat">
                        <div class="char-stat-label">🦉 Мудрость</div>
                        <div class="char-stat-value"><?= $team['wisdom'] ?></div>
                    </div>
                    <div class="char-stat">
                        <div class="char-stat-label">💬 Харизма</div>
                        <div class="char-stat-value"><?= $team['charisma'] ?></div>
                    </div>
                    <div class="char-stat">
                        <div class="char-stat-label">⚡ Иниц.</div>
                        <div class="char-stat-value"><?= $team['initiative'] ?></div>
                    </div>
                    <div class="char-stat">
                        <div class="char-stat-label">🏃 Скорость</div>
                        <div class="char-stat-value"><?= $team['speed'] ?></div>
                    </div>
                </div>
                
                <?php if ($team['ability1'] || $team['ability2'] || $team['ability3']): ?>
                <div class="abilities">
                    <h4>🔮 Способности:</h4>
                    <?php if ($team['ability1']): ?>
                        <div class="ability-item"><strong>1.</strong> <?= htmlspecialchars($team['ability1']) ?></div>
                    <?php endif; ?>
                    <?php if ($team['ability2']): ?>
                        <div class="ability-item"><strong>2.</strong> <?= htmlspecialchars($team['ability2']) ?></div>
                    <?php endif; ?>
                    <?php if ($team['ability3']): ?>
                        <div class="ability-item"><strong>3.</strong> <?= htmlspecialchars($team['ability3']) ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($team['item1'] || $team['item2'] || $team['item3']): ?>
                <div class="items">
                    <h4>🎒 Предметы:</h4>
                    <?php if ($team['item1']): ?>
                        <div class="item-item"><strong>1.</strong> <?= htmlspecialchars($team['item1']) ?></div>
                    <?php endif; ?>
                    <?php if ($team['item2']): ?>
                        <div class="item-item"><strong>2.</strong> <?= htmlspecialchars($team['item2']) ?></div>
                    <?php endif; ?>
                    <?php if ($team['item3']): ?>
                        <div class="item-item"><strong>3.</strong> <?= htmlspecialchars($team['item3']) ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="students-list">
            <h3>👥 Члены команды</h3>
            <?php if (count($students) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Фамилия</th>
                        <th>Имя</th>
                        <th>Отчество</th>
                        <th>Баллы</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $position = 1; ?>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= $position++ ?></td>
                        <td><?= htmlspecialchars($student['last_name']) ?></td>
                        <td><?= htmlspecialchars($student['first_name']) ?></td>
                        <td><?= htmlspecialchars($student['middle_name'] ?? '-') ?></td>
                        <td><strong><?= $student['score'] ?> 💰</strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="no-data">В этой команде пока нет студентов</div>
            <?php endif; ?>
        </div>
        
        <?php elseif ($team_id): ?>
        <div class="no-data">
            <h3>Команда не найдена</h3>
        </div>
        <?php else: ?>
        <div class="no-data">
            <h3>Выберите команду для просмотра статистики</h3>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>