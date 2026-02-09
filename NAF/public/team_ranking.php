<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
checkAuth();

$pdo = getDBConnection();

// Получаем рейтинг команд (с учетом триггера, который автоматически обновляет amount)[1]
$stmt = $pdo->query("
    SELECT t.team_id, t.team_color, t.amount, t.inspiration,
           COUNT(s.student_id) as members_count,
           c.name as character_name, c.level as character_level
    FROM TEAMS t
    LEFT JOIN STUDENTS s ON t.team_id = s.team_id
    LEFT JOIN CHARACTERS c ON t.character_id = c.character_id
    GROUP BY t.team_id
    ORDER BY t.amount DESC, t.inspiration DESC
");
$teams = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Рейтинг команд</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .header { background: #333; color: white; padding: 20px; }
        .logout { float: right; background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 20px; }
        .ranking-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .ranking-header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 20px; margin: -30px -30px 20px -30px; border-radius: 8px 8px 0 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #007bff; color: white; font-weight: bold; }
        tr:hover { background: #f8f9fa; }
        .rank { font-size: 24px; font-weight: bold; text-align: center; }
        .rank-1 { color: #ffd700; }
        .rank-2 { color: #c0c0c0; }
        .rank-3 { color: #cd7f32; }
        .team-link { color: #007bff; text-decoration: none; font-weight: bold; }
        .team-link:hover { text-decoration: underline; }
        .back { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin-bottom: 20px; }
        .nav-links { margin-bottom: 20px; }
        .nav-links a { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-right: 10px; display: inline-block; }
        .nav-links a:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏆 Рейтинг команд</h1>
        <a href="../logout.php" class="logout">Выход</a>
        <div style="clear: both;"></div>
        <p>Пользователь: <?= htmlspecialchars($_SESSION['username']) ?> (<?= $_SESSION['role'] ?>)</p>
    </div>
    
    <div class="container">
        <div class="nav-links">
            <?php if (isAdmin()): ?>
                <a href="../admin/dashboard.php">← Админ панель</a>
            <?php elseif (isTeacher()): ?>
                <a href="../teacher/dashboard.php">← Панель учителя</a>
            <?php endif; ?>
            <a href="student_ranking.php">📊 Рейтинг студентов</a>
            <a href="bestiary_view.php">🐉 Бестиарий</a>
        </div>
        
        <div class="ranking-container">
            <div class="ranking-header">
                <h2>🏆 Турнирная таблица команд</h2>
                <p>Сортировка по общему количеству баллов и вдохновению</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px; text-align: center;">Место</th>
                        <th>Команда</th>
                        <th>Персонаж</th>
                        <th style="text-align: center;">Членов</th>
                        <th style="text-align: center;">💰 Баллы</th>
                        <th style="text-align: center;">✨ Вдохновение</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rank = 1;
                    foreach ($teams as $team): 
                        $rankClass = '';
                        if ($rank == 1) $rankClass = 'rank-1';
                        elseif ($rank == 2) $rankClass = 'rank-2';
                        elseif ($rank == 3) $rankClass = 'rank-3';
                    ?>
                    <tr>
                        <td class="rank <?= $rankClass ?>"><?= $rank ?></td>
                        <td>
                            <strong><?= htmlspecialchars($team['team_color']) ?></strong>
                        </td>
                        <td>
                            <?php if ($team['character_name']): ?>
                                <?= htmlspecialchars($team['character_name']) ?> (ур. <?= $team['character_level'] ?>)
                            <?php else: ?>
                                <span style="color: #999;">Не назначен</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;"><?= $team['members_count'] ?></td>
                        <td style="text-align: center;"><strong><?= $team['amount'] ?></strong></td>
                        <td style="text-align: center;"><strong><?= $team['inspiration'] ?></strong></td>
                        <td>
                            <a href="team_stats.php?team_id=<?= $team['team_id'] ?>" class="team-link">Подробнее →</a>
                        </td>
                    </tr>
                    <?php 
                    $rank++;
                    endforeach; 
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>