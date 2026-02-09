<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
checkAuth();

$pdo = getDBConnection();

// Получаем рейтинг студентов[1]
$stmt = $pdo->query("
    SELECT s.student_id, s.first_name, s.last_name, s.middle_name, s.score,
           t.team_color, t.team_id
    FROM STUDENTS s
    LEFT JOIN TEAMS t ON s.team_id = t.team_id
    ORDER BY s.score DESC, s.last_name, s.first_name
");
$students = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Рейтинг студентов</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .header { background: #333; color: white; padding: 20px; }
        .logout { float: right; background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 20px; }
        .ranking-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .ranking-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; margin: -30px -30px 20px -30px; border-radius: 8px 8px 0 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #007bff; color: white; font-weight: bold; }
        tr:hover { background: #f8f9fa; }
        .rank { font-size: 24px; font-weight: bold; text-align: center; }
        .rank-1 { color: #ffd700; }
        .rank-2 { color: #c0c0c0; }
        .rank-3 { color: #cd7f32; }
        .back { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin-bottom: 20px; }
        .nav-links { margin-bottom: 20px; }
        .nav-links a { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-right: 10px; display: inline-block; }
        .nav-links a:hover { background: #0056b3; }
        .team-badge { display: inline-block; padding: 4px 10px; background: #28a745; color: white; border-radius: 12px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Рейтинг студентов</h1>
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
            <?php elseif (isCaptain() && $_SESSION['team_id']): ?>
                <a href="team_stats.php?team_id=<?= $_SESSION['team_id'] ?>">← Моя команда</a>
            <?php endif; ?>
            <a href="team_ranking.php">🏆 Рейтинг команд</a>
            <a href="bestiary_view.php">🐉 Бестиарий</a>
        </div>
        
        <div class="ranking-container">
            <div class="ranking-header">
                <h2>📊 Турнирная таблица студентов</h2>
                <p>Сортировка по количеству баллов</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px; text-align: center;">Место</th>
                        <th>Фамилия</th>
                        <th>Имя</th>
                        <th>Отчество</th>
                        <th>Команда</th>
                        <th style="text-align: center;">💰 Баллы</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rank = 1;
                    foreach ($students as $student): 
                        $rankClass = '';
                        if ($rank == 1) $rankClass = 'rank-1';
                        elseif ($rank == 2) $rankClass = 'rank-2';
                        elseif ($rank == 3) $rankClass = 'rank-3';
                    ?>
                    <tr>
                        <td class="rank <?= $rankClass ?>"><?= $rank ?></td>
                        <td><strong><?= htmlspecialchars($student['last_name']) ?></strong></td>
                        <td><?= htmlspecialchars($student['first_name']) ?></td>
                        <td><?= htmlspecialchars($student['middle_name'] ?? '-') ?></td>
                        <td>
                            <?php if ($student['team_color']): ?>
                                <span class="team-badge"><?= htmlspecialchars($student['team_color']) ?></span>
                            <?php else: ?>
                                <span style="color: #999;">Без команды</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center; font-size: 18px;"><strong><?= $student['score'] ?></strong></td>
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